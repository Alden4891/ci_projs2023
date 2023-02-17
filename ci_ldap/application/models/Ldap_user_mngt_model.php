<?php

class Ldap_user_mngt_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    // Connect to the LDAP server
    // 12-DC-02.ENTDSWD.LOCAL
    // 172.26.134.11
    // 172.16.10.10



    try {
        $this->ldapconn = ldap_connect("172.16.10.10");
        // print('connected to 172.10.10.16');
    } catch(Exception $e) {
        $this->ldapconn = ldap_connect("172.26.134.11");
        // print('connected to 172.26.134.11');
    } 
    // fsockopen('172.10.10.16', 389);

    if ($this->ldapconn) {
      // Bind to the LDAP server
      ldap_set_option($this->ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
      ldap_set_option($this->ldapconn, LDAP_OPT_REFERRALS, 0);


      // $this->ldapbind = @ldap_bind($this->ldapconn, "ENTDSWD\\etraining", "dswd@123");
      $this->ldapbind = ldap_bind($this->ldapconn, "ENTDSWD\\aaquinones", "protectme@4891021408271209");
      if ($this->ldapbind) {
        // print('<br>bind successful! <hr>');  
      }
      
    }
  }




  private function ldap_timestamp_to_date($timestamp) {
      $res = @date('Y-m-d', ($timestamp / 10000000) - 11644473600);
      return ($res == '1601-01-01'?'':$res);

  }

  private function ldap_ymd_timestamp_to_date($date, $format = 'd.m.Y H:i:s') {

      // Get the individual date segments by splitting up the LDAP date
      $y = substr($date,0,4);
      $m = substr($date,4,2);
      $d = substr($date,6,2);
      $h = substr($date,8,2);
      $i = substr($date,10,2);
      $s = substr($date,12,2);

      // Make the Unix timestamp from the individual parts
      $timestamp = mktime($h, $i, $s, $m, $d, $y);

      // Output the 'never' for 01.01.1970 01:00, 30.11.1999 00:00, or after now
      if($timestamp == 0 || $timestamp == 943916400 || $timestamp > time())
          return 'never';

      // Output the finished timestamp
      return date($format, $timestamp);
  }




  // --------------------------------------------------------------------------------------

  
  public function get_all_ldap_users_in_ou($ou) {
    $ldap_conn = $this->ldapconn;
    if (!$ldap_conn) {
      return false;
    }

    $base_dn = $ou.",DC=ENTDSWD,DC=LOCAL";
    $filter = "(objectClass=person)";
    // $filter = "(sAMAccountName=etraining)";
    $attributes = array(
        "cn"
      , "samaccountname"
      , "pwdupdatetime"
      , "department"
      , "company"
      , "userprincipalname"
      , "mail"
      , "lastlogon"
      , "pwdaccountlockedtime"
    );

    $result = @ldap_search($ldap_conn, $base_dn, $filter);
    if (!$result) {
      return false;
    }

    $entries = ldap_get_entries($ldap_conn, $result);
    
    $result_count = $entries["count"];
    print($result_count);
    $ldap_users = array();
    for ($i = 0; $i < $result_count; $i++) {
      // echo "<hr><pre>";
      // print_r($entries);
      // echo "</pre>";

      $timestamp = @strtotime($entries[$i]["pwdupdatetime"][0]);

      // Check if the account never expires
      $account_never_expires = "No";
      if (isset($entries[$i]["accountexpires"][0])) {
        if ($entries[$i]["accountexpires"][0] == 9223372036854775807) {
          $account_never_expires = "Yes";
        }
      }


      //expiry date
      $accountExpires = @$entries[$i]["accountExpires"][0];
      $expiry = (date('Y-m-d', ($accountExpires / 10000000) - 11644473600)?'No-Expiry':date('Y-m-d', ($accountExpires / 10000000) - 11644473600));


      //check if disabled or not
      $account_disabled = $entries[$i]["useraccountcontrol"][0] & 2 ? "Disabled" : "Enabled";

      //get last logged-in
      $last_login = @date('Y-m-d H:i:s', ($entries[0]['lastLogonTimestamp'][0] / 10000000) - 11644473600);


      $ldap_user = array(
         "samaccountname" => $entries[$i]["samaccountname"][0]
        ,"cn" => $entries[$i]["cn"][0]
        ,"department" => @$entries[$i]["department"][0]
        ,"description" => @$entries[$i]["description"][0]
        ,"company" => @$entries[$i]["company"][0]
        ,"userprincipalname" => @$entries[$i]["userprincipalname"][0]
        ,"lockouttime" => $this->ldap_timestamp_to_date(@$entries[$i]["lockouttime"][0])
        ,"pwd_last_changed" => $this->ldap_timestamp_to_date(@$entries[$i]["pwdupdatetime"][0])
        ,"expiration_date" => $expiry
        ,"never_expire" => $account_never_expires
        ,"account_status" => $account_disabled
        ,"whenchanged" => @$this->ldap_ymd_timestamp_to_date(@$entries[$i]["whenchanged"][0])
        ,"whencreated" => @$this->ldap_ymd_timestamp_to_date(@$entries[$i]["whencreated"][0])
        ,"area_of_assignment" => @$entries[$i]["streetaddress"][0]
        ,"lastlogontimestamp" => $this->ldap_timestamp_to_date(@$entries[$i]["lastlogontimestamp"][0])
        ,"PwdLastSet" => $this->ldap_timestamp_to_date(@$entries[$i]["PwdLastSet"][0])
        ,"memberof" => @$entries[$i]["memberof"]
      );
      $ldap_users[] = $ldap_user;
    }
    return $ldap_users; 
    // return 1;
  }


  // --------------------------------------------------------------------------------------


  public function get_user_groups($username,$mode = 'all') {
    $basedn = "DC=ENTDSWD,DC=LOCAL";
    if ($this->ldapbind) {
      // Search for the user account with the specified username
      $search = ldap_search($this->ldapconn, $basedn, "(&(objectClass=person)(samaccountname=$username))");
      $entries = ldap_get_entries($this->ldapconn, $search);
      // print_r($entries);
      if ($entries["count"] == 1) {
        $entry = $entries[0];
        $groups = array();
        if (isset($entry["memberof"])) {
          for ($i=0; $i<$entry["memberof"]["count"]; $i++) {
            $group = $entry["memberof"][$i];
            if ($mode == 'name_only') {
              $parts = ldap_explode_dn($group, 1);
              $group = $parts[0];
            }
            // Extract the group name from the DN of the group
            
            $groups[] = $group;
          }
          return $groups;
        }
      }
    }
    return false;
  }




  public function get_all_groups() {
    // $base_dn = "DC=ENTDSWD,DC=LOCAL";
      $base_dn = "OU=FO12,DC=ENTDSWD,DC=LOCAL";
    if ($this->ldapbind) {
      
      // Search for all groups in the specified DN
      $search = ldap_search($this->ldapconn, $base_dn, "(&(cn=12*)(objectclass=group))");
      $entries = ldap_get_entries($this->ldapconn, $search);
      // print('<pre>');
      // print_r($entries);
      // print('</pre>');

      $groups = array();
      for ($i=0; $i<$entries["count"]; $i++) {
        $entry = @$entries[$i]['distinguishedname'];
        $groups[] = $entry[0];
      }
      return $groups;

    }
    return false;
  }



}
