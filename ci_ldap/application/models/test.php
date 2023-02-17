<?php

class test extends CI_Model {

  public function __construct() {
    parent::__construct();
    // Connect to the LDAP server
    // 12-DC-02.ENTDSWD.LOCAL
    // 172.26.134.11
    // 172.16.10.10



    try {
        $this->ldapconn = ldap_connect("172.16.10.10");
        print('connected to 172.10.10.16');
    } catch(Exception $e) {
        $this->ldapconn = ldap_connect("172.26.134.11");
        print('connected to 172.26.134.11');
    } 
    // fsockopen('172.10.10.16', 389);

    if ($this->ldapconn) {
      // Bind to the LDAP server
      ldap_set_option($this->ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
      ldap_set_option($this->ldapconn, LDAP_OPT_REFERRALS, 0);


      // $this->ldapbind = @ldap_bind($this->ldapconn, "ENTDSWD\\etraining", "dswd@123");
      $this->ldapbind = ldap_bind($this->ldapconn, "ENTDSWD\\aaquinones", "protectme@4891021408271209");
      if ($this->ldapbind) {
        print('<br>bind successful! <hr>');  
      }
      
    }
  }




  public function get_expiring_accounts($ou) {
    if ($this->ldapbind) {
      // Search for all user accounts within the specified OU
      $base_dn = $ou.",DC=ENTDSWD,DC=LOCAL";
      $search = ldap_search($this->ldapconn, $base_dn, "(objectClass=inetOrgPerson)");
      $entries = ldap_get_entries($this->ldapconn, $search);
      print_r($entries);
      // Filter the entries to find only accounts that will expire within a month
      $expiring_accounts = array();
      for ($i=0; $i < $entries["count"]; $i++) {
        $entry = $entries[$i];
        $expiry_date = $entry["shadowexpire"][0];
        if ($expiry_date != -1 && $expiry_date <= time() + 30 * 24 * 60 * 60) {
          $expiring_accounts[] = $entry;
        }
      }
      return $expiring_accounts;
    }
  }



    function generate_random_password() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

  //[working/reset untested] 
  function bulk_change_expired_passwords($ou) {
    $base_dn = $ou.",DC=ENTDSWD,DC=LOCAL";
        $result = ldap_search($this->ldapconn, $base_dn, '(&(objectClass=person)(accountExpires<='.time().'))', array('cn', 'dn','sAMAccountName','accountExpires'));
        $entries = ldap_get_entries($this->ldapconn, $result);


        // echo "<hr><pre>";
        // print_r($entries);
        // echo "</pre>";


        if (!empty($result)) {
            $updated_accounts = array();
            foreach ($entries as $key => $value) {
                $username = @$value['cn'][0];
                $samaccountname = @$value['samaccountname'][0];
                $dn = @$value['dn'];
                $new_password = $this->generate_random_password();
                // $this->ldap->modify($dn, array('userPassword' => $new_password));

                $updated_accounts[] = array(
                    'name' => $username,
                    'samaccountname' => $samaccountname,
                    'new_password' => $new_password,
                    'accountExpires' => @$value['accountExpires'][0]
                );
            }
            return $updated_accounts;
        } else {
            return false;
        }
    }


  // --------------------------------------------------------------------------------------
  // [QUEUE]

  public function unlock_account($username) {
    if ($this->ldapbind) {
      // Search for the user account with the specified username
      $search = ldap_search($this->ldapconn, $this->basedn, "(&(objectClass=inetOrgPerson)(uid=$username))");
      $entries = ldap_get_entries($this->ldapconn, $search);
      if ($entries["count"] == 1) {
        $entry = $entries[0];
        $dn = $entry["dn"];
        // Unlock the account by resetting the shadowflag attribute
        $data = array("shadowflag" => "0");
        if (ldap_modify($this->ldapconn, $dn, $data)) {
          // Enable the account by resetting the useraccountcontrol attribute
          $data = array("useraccountcontrol" => "512");
          if (ldap_modify($this->ldapconn, $dn, $data)) {
            return true;
          }
        }
      }
      return false;
    }
  }

  public function is_account_locked($username) {
    if ($this->ldapbind) {
      $basedn = "OU=PPIS ML,OU=Clients,OU=FO12".",DC=ENTDSWD,DC=LOCAL";
      // Search for the user account with the specified username
      $search = ldap_search($this->ldapconn, $basedn, "(&(objectClass=inetOrgPerson)(uid=$username))");
      $entries = ldap_get_entries($this->ldapconn, $search);
      if ($entries["count"] == 1) {
        $entry = $entries[0];
        // Check if the account is locked
        if (array_key_exists("shadowflag", $entry) && $entry["shadowflag"][0] == "1") {
          return true;
        }
        // Check if the account is disabled
        if (array_key_exists("useraccountcontrol", $entry) && $entry["useraccountcontrol"][0] == "514") {
          return true;
        }
      }
      return false;
    }
  }




  public function delete_user($username) {
    // Connect to LDAP server
    // $ldapconn = ldap_connect("ldap://ldap.example.com") or die("Could not connect to LDAP server.");
    $ldapconn = $this->ldapconn;


    // Bind to the LDAP directory
    ldap_bind($ldapconn, "cn=admin,dc=example,dc=com", "secret")
      or die("LDAP bind failed.");

    // Search for the user
    $result = ldap_search($ldapconn, "ou=people,dc=example,dc=com", "uid=$username")
      or die("LDAP search failed.");

    // Get the user DN
    $entries = ldap_get_entries($ldapconn, $result);
    $userdn = $entries[0]["dn"];

    // Delete the user
    if (ldap_delete($ldapconn, $userdn)) {
      return "LDAP user $username deleted successfully.";
    } else {
      return "LDAP user deletion failed.";
    }

    // Close the connection
    ldap_close($ldapconn);
  }

  # NOT WORKING

  public function set_user_group($username, $group) {
    if ($this->ldapbind) {
      // Search for the user account with the specified username
      $search = ldap_search($this->ldapconn, $this->basedn, "(&(objectClass=inetOrgPerson)(uid=$username))");
      $entries = ldap_get_entries($this->ldapconn, $search);
      if ($entries["count"] == 1) {
        $entry = $entries[0];
        $dn = $entry["dn"];
        // Add the group to the user's group membership
        $newdata["memberof"][] = "cn=$group,ou=groups,dc=example,dc=com";
        $result = ldap_mod_add($this->ldapconn, $dn, $newdata);
        if ($result) {
          return true;
        }
      }
      return false;
    }
  }


  public function reset_password($dn, $new_password) {
    if ($this->ldapbind) {
      // Reset the user password by modifying the userPassword attribute
      $entry = array();
      $entry["userPassword"] = "{CLEARTEXT}" . $new_password;
      if (ldap_mod_replace($this->ldapconn, $dn, $entry)) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function lock_user($dn) {
    if ($this->ldapbind) {
      // Lock the user account by setting the pwdAccountLockedTime attribute
      $entry = array();
      $entry["pwdAccountLockedTime"] = "000001010000Z";
      if (ldap_mod_add($this->ldapconn, $dn, $entry)) {
        return true;
      } else {
        return false;
      }
    }
  }



  public function create_user($ou) {
    if ($this->ldapbind) {
      // Set the distinguished name for the new user
      // Define the new user attributes
      $entry = array();
      $entry["objectClass"] = array("top", "person", "inetOrgPerson");

      $entry["givenname"] = 'Juanna Jane';
      $entry["initials"] = 'a';
      $entry["sn"] = "dela cruz";

      $entry["cn"] = $entry["givenname"] . ' ' . $entry["initials"] . ' ' . $entry["sn"];      
      $entry["uid"] = 'JJADELACRUZ';
      $entry["userPassword"] = "{CLEARTEXT}" . 'dswd@123';
      $entry["mail"] = 'JJADELACRUZ@gmail.com';


      $dn = "cn=" . $entry["cn"] .",". $ou.",DC=ENTDSWD,DC=LOCAL";

      // Add the new user to the LDAP directory
      if (ldap_add($this->ldapconn, $dn, $entry)) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function clone_user($existing_dn, $new_cn) {
    if ($this->ldapbind) {
      // Search for the existing user
      $search = ldap_search($this->ldapconn, $existing_dn, "(objectClass=*)");
      $existing_user = ldap_first_entry($this->ldapconn, $search);
      $existing_attrs = ldap_get_attributes($this->ldapconn, $existing_user);
      // Create an array of the existing user's attributes
      $entry = array();
      for ($i=0; $i < $existing_attrs["count"]; $i++) {
        $entry[$existing_attrs[$i]] = $existing_attrs[$existing_attrs[$i]][0];
      }
      // Update the new user's distinguished name and common name
      $entry["dn"] = str_replace("cn=" . $entry["cn"], "cn=" . $new_cn, $existing_dn);
      $entry["cn"] = $new_cn;
      // Add the new user to the LDAP directory
      if (ldap_add($this->ldapconn, $entry["dn"], $entry)) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function get_expired_accounts($ou) {
    if ($this->ldapbind) {
      // Search for all user accounts within the specified OU
      $search = ldap_search($this->ldapconn, $ou, "(objectClass=inetOrgPerson)");
      $entries = ldap_get_entries($this->ldapconn, $search);
      // Filter the entries to find only expired accounts
      $expired_accounts = array();
      for ($i=0; $i < $entries["count"]; $i++) {
        $entry = $entries[$i];
        $expiry_date = $entry["shadowexpire"][0];
        if ($expiry_date != -1 && $expiry_date <= time()) {
          $expired_accounts[] = $entry;
        }
      }
      return $expired_accounts;
    }
  }


  public function is_account_disabled($username) {
    if ($this->ldapbind) {
      // Search for the user account with the specified username
      $search = ldap_search($this->ldapconn, $this->basedn, "(&(objectClass=inetOrgPerson)(uid=$username))");
      $entries = ldap_get_entries($this->ldapconn, $search);
      if ($entries["count"] == 1) {
        $entry = $entries[0];
        // Check if the account is disabled
        if (array_key_exists("useraccountcontrol", $entry) && ($entry["useraccountcontrol"][0] & 2)) {
          return true;
        }
      }
      return false;
    }
  }





}
