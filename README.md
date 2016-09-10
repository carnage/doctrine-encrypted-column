**Work in progress**

# Motivation

Currently there are about a dozen encrypted column extensions for doctrine. None of them are very well implemented and are
thus insecure (eg using Pop-art mode (ECB) or auto decrypting data on load) most also are tied to a framework making them
useless unless you use that framework.

This lib intends to resolve these two issues and provide an obvious choice library for anyone needing to encrypt data they
are storing through doctrine ORM.

# Pull requests

I will accept pull requests for the following:

- New serialisation support (JMS is desirable here)
- Support for doctrine ODM
- Support for different crypto backends which use a good implementation (eg Zend crypt, defuse, easyrsa)
- bug fixes

I will not accept:

- Integration into << your favorite framework >> create a lib for that which uses this and PR a link for the readme
- Support for poor crypto implementations (eg anything using mcrypt)


# Security issues

Once this lib is tagged, I will provide an email + GPG keys for submitting security issues to, until then please raise
github issues.