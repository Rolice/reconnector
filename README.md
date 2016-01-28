# Reconnector
This package provides a slight replacement of the default connection mechanism in Laravel 5.1+. The idea is to enable
one application to work with several database connections in order to increase the availability in case of server
failure. This is useful when using a database cluster which cannot route incoming connections to the nodes available.
