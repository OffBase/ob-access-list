# Off Base Access List

This plugin adds the ability to add locations that require an access list.

IPs can be added to the access list from within the Network Setting of the Network Admin, which means only Super Admins can edit the IP access list. If the access list is empty, no access list is applied to the locations specified. One IP, Range or CIDR is specified per line within the Access List text area.

The following types of IPs are allowed to be added to the access list:
IPv4 / IPv6 Single
IPv4 / IPv6 Range
IPv4 CIDR

Currently, the locations that are access listed are hardcoded. The consist of the following:
`wp-login.php`
`wp-admin`
`wp-activate.php`
`wp-links-opml.php`
`wp-trackback.php`
