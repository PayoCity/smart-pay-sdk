<p align="center">
  <img src="##" width="128" height="128"/>
</p>

<h1 align="center">Rabo Smart Pay SDK client for PHP 8.5+</h1>

## Requirements ##
To use the Rabo Smart Pay SDK, the following things are required:

+ Have an active Rabo Smart Pay account, with activated Payment Brands.
+ Install the Rabo Smart Pay SDK with composer and integrate it.

+ PHP >= 8.5
+ Up-to-date OpenSSL library

## Composer Installation ##

By far the easiest way to install the HUB API client is to require it with [Composer](http://getcomposer.org/doc/00-intro.md).



    $ composer require **/**

    {
        "require": {
            "**/**": "1.*"
        }
    }


## Refresh Token ##

## Signing Key ##

## Access Token ##





## Left out for now, can be requested

# Billing Detail && Shipping Detail

This structure contains address details and is typically used
in specifying billing and shipping details of an order.

The array should contain keys such as:
'firstName', 'middleName', 'lastName', 'street',
'houseNumber', 'houseNumberAddition', 'postalCode',
'city', and 'countryCode'.
Each key should have a corresponding value representing the
respective address detail.


# Order Items

An optional list of zero or more order items.

The array should contain keys such as:
'amount', 'category', 'description', 'id', 'name', 'quantity', 'tax',  'vatCategory'
Each key should have a corresponding value representing the
respective order line.
