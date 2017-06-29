#Doctrine Encryted Column
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/carnage/doctrine-encrypted-column/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/carnage/doctrine-encrypted-column/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/carnage/doctrine-encrypted-column/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/carnage/doctrine-encrypted-column/?branch=master)

# Motivation

Currently there are about a dozen encrypted column extensions for doctrine. None of them are very well implemented and are
thus insecure (eg using Pop-art mode (ECB) or auto decrypting data on load) most also are tied to a framework making them
useless unless you use that framework.

This lib intends to resolve these two issues and provide an obvious choice library for anyone needing to encrypt data they
are storing through doctrine ORM.

Every endeavour will be taken to ensure that future versions of this library will be able to read data encrypted with
older versions and re-encrypt to take advantage of any security fixes or improvements. In the event that this isn't
possible automatically, guidance will be provided to allow you to migrate your data manually, to ensure that this process
is as smooth as possible, we suggest making a note of the versions of lib sodium, halite and this library that you initially
install.

# Features

- Encrypted column type for doctrine
- Functionally similar to object column type
- Transparent to end user
- Uses proxies to avoid decrypting data that isn't needed
- Best in class cryptography (LibSodium)

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

You can use my keys from keybase https://keybase.io/carnage to contact me regarding any security issues.