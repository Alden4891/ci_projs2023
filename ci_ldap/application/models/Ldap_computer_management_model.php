<?php

class Ldap_computer_management_model extends CI_Model {

   public function list_computers_and_last_logged_in() {
      // Connect to LDAP server
      $ldapconn = ldap_connect("ldap://ldap.example.com")
        or die("Could not connect to LDAP server.");

      // Bind to the LDAP directory
      ldap_bind($ldapconn, "cn=admin,dc=example,dc=com", "secret")
        or die("LDAP bind failed.");

      // Search for all computer objects
      $result = ldap_search($ldapconn, "ou=computers,dc=example,dc=com", "(objectclass=computer)")
        or die("LDAP search failed.");

      // Get the computer entries
      $entries = ldap_get_entries($ldapconn, $result);

      // Loop through the computer entries
      for ($i=0; $i<$entries["count"]; $i++) {
        // Get the last logged-in user DN
        $last_logged_in = $entries[$i]["lastlogon"][0];

        // Search for the last logged-in user
        $user_result = ldap_search($ldapconn, $last_logged_in, "(objectclass=person)")
          or die("LDAP search failed.");

        // Get the last logged-in user entry
        $user_entries = ldap_get_entries($ldapconn, $user_result);

        // Get the last logged-in username
        $entries[$i]["last_logged_in_username"] = $user_entries[0]["cn"][0];
      }

      // Close the connection
      ldap_close($ldapconn);

      // Return the computer entries
      return $entries;
  }
}