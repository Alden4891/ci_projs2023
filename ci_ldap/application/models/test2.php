<?php

// Connection parameters
$ldap_host = "ldap.example.com";
$ldap_dn = "dc=example,dc=com";
$ldap_user = "cn=admin,dc=example,dc=com";
$ldap_password = "secret";

// Connect to LDAP server
$ldap = ldap_connect($ldap_host)
    or die("Could not connect to LDAP server.");

// Bind to LDAP server
ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
$bind = ldap_bind($ldap, $ldap_user, $ldap_password);

// Check if the user has the privilege to create new LDAP accounts
$dn = "uid=user1,ou=people,dc=example,dc=com";
$user = "user1";
$password = "secret";

// Check if the user is authorized to create new LDAP accounts
$result = ldap_search($ldap, $dn, "(cn=$user)");
$entries = ldap_get_entries($ldap, $result);
if ($entries["count"] > 0) {
    $dn = $entries[0]["dn"];
    if (ldap_bind($ldap, $dn, $password)) {
        // User is authorized, create new LDAP account
        $newUser = array();
        $newUser["objectclass"][0] = "top";
        $newUser["objectclass"][1] = "person";
        $newUser["objectclass"][2] = "organizationalPerson";
        $newUser["objectclass"][3] = "inetOrgPerson";
        $newUser["cn"] = "John Doe";
        $newUser["sn"] = "Doe";
        $newUser["givenname"] = "John";
        $newUser["mail"] = "johndoe@example.com";
        $newUser["userpassword"] = "{CLEARTEXT}secret";
        $newUser["uid"] = "johndoe";
        $newUser["ou"] = "people";
        $newUserDn = "uid=johndoe,ou=people," . $ldap_dn;
        $r = ldap_add($ldap, $newUserDn, $newUser);
        if ($r) {
            echo "LDAP account created successfully.";
        } else {
            echo "LDAP account creation failed.";
        }
    } else {
        echo "User is not authorized to create new LDAP accounts.";
    }
} else {
    echo "User does not exist.";
}

// Close connection
ldap_close($ldap);

?>
