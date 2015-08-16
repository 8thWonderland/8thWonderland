<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

class CustomAJAXChat extends AJAXChat {

	// Returns an associative array containing userName, userID and userRole
	// Returns null if login is invalid
    
	function getValidLoginUserData() {
            
		$customUsers = $this->getCustomUsers();
		
		if($this->getRequestVar('password')) {
			// Check if we have a valid registered user:

			$userName = $this->getRequestVar('userName');
			$userName = $this->convertEncoding($userName, $this->getConfig('contentEncoding'), $this->getConfig('sourceEncoding'));

			$password = $this->getRequestVar('password');
			$password = $this->convertEncoding($password, $this->getConfig('contentEncoding'), $this->getConfig('sourceEncoding'));

			foreach($customUsers as $key=>$value) {
                            if(strtolower($value['userName']) == strtolower($userName)) {
                                if ($value['password'] == hash('sha512', $password)) {
                                    $userData = array();
                                    $userData['userID'] = $key;
                                    $userData['userName'] = $this->trimUserName($value['userName']);
                                    $userData['userRole'] = $value['userRole'];
                                    return $userData;
                                }
                            }
			}
			
			return null;
		} else {
                    $identity_found = false;
                    $userName = $this->getRequestVar('userName');
                    foreach($customUsers as $key=>$value) {
                        if(strtolower($value['userName']) == strtolower($userName)) {
                            $identity_found = true; break;
                        }
                    }
                    if ($identity_found)    {   return null;                    }
                    else                    {   return $this->getGuestUser();   }
		}
	}

	// Store the channels the current user has access to
	// Make sure channel names don't contain any whitespace
	function &getChannels() {
		if($this->_channels === null) {
			$this->_channels = array();
			
			$customUsers = $this->getCustomUsers();
			
			// Get the channels, the user has access to:
			if($this->getUserRole() == AJAX_CHAT_GUEST) {
				$validChannels = $customUsers[0]['channels'];
			} else {
				$validChannels = $customUsers[$this->getUserID()]['channels'];
			}
			
			// Add the valid channels to the channel list (the defaultChannelID is always valid):
			foreach($this->getAllChannels() as $key=>$value) {
				// Check if we have to limit the available channels:
				if($this->getConfig('limitChannelList') && !in_array($value, $this->getConfig('limitChannelList'))) {
					continue;
				}
				
				if(in_array($value, $validChannels) || $value == $this->getConfig('defaultChannelID')) {
					$this->_channels[$key] = $value;
				}
			}
		}
		return $this->_channels;
	}

	// Store all existing channels
	// Make sure channel names don't contain any whitespace
	function &getAllChannels() {
		if($this->_allChannels === null) {
			// Get all existing channels:
			$customChannels = $this->getCustomChannels();
			
			$defaultChannelFound = false;
			
			foreach($customChannels as $key=>$value) {
				$forumName = $this->trimChannelName($value);
				
				$this->_allChannels[$forumName] = $key;
				
				if($key == $this->getConfig('defaultChannelID')) {
					$defaultChannelFound = true;
				}
			}
			
			if(!$defaultChannelFound) {
				// Add the default channel as first array element to the channel list:
				$this->_allChannels = array_merge(
					array(
						$this->trimChannelName($this->getConfig('defaultChannelName'))=>$this->getConfig('defaultChannelID')
					),
					$this->_allChannels
				);
			}
		}
		return $this->_allChannels;
	}

	function &getCustomUsers() {
		// List containing the registered chat users:
		$users = null;
                $db = new mysqli("mysql5-2.300gp", "thwonderbdd", "j5pk8yG6pc7", "thwonderbdd");
                if (mysqli_connect_error()) {
                    throw new exception('Database connect failure : ' . mysqli_connect_error());
                }
                
                $req = "SELECT DISTINCT iduser, identite, password FROM Utilisateurs";
                $res = $db->query($req);
                $num_row = 0;
                while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                    $test_dev = $db->query("SELECT COUNT(*) FROM Citizen_Groups WHERE Citizen_id=" . $row['iduser'] . " AND Group_id=1");
                    $dev = $test_dev->fetch_row();
                    
                    if ($dev[0]>0)      {   $users[$num_row]['userRole'] = AJAX_CHAT_ADMIN;  }
                    else                {   $users[$num_row]['userRole'] = AJAX_CHAT_USER;  }
                    $users[$num_row]['userName'] = $row['identite'];
                    $users[$num_row]['password'] = $row['password'];
                    $users[$num_row]['channels'] = array(0);
                    $num_row++;
                }
                
                
		//require(AJAX_CHAT_PATH.'lib/data/users.php');
		return $users;
	}
	
	function &getCustomChannels() {
		// List containing the custom channels:
		$channels = null;
		require(AJAX_CHAT_PATH.'lib/data/channels.php');
		return $channels;
	}

}
?>