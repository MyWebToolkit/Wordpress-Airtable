# Wordpress-Airtable
Sample code for updating Airtable from Wordpress

# Purpose

This project provides a reference point for anyone seeking to integrate Wordpress with Airtable for the purpose of displaying Airtable data in Wordpress and, more importantly, update the Airtable data from Wordpress.

It is motivated by the fact that, as of the time of writing this, information, and examples, on how to update airtables from within wordpress, can be challenging to the first timer.

# Approach

The project provides guidance on how to create a working proof of concept of a Wordpress - Airtable integration.

It utilizes a Wordpress Plugin, uses wp_remote_get() rather than directly using curl. The plugin can be configured to work with a free Airtable account and a standard Airtable Base template, with minor changes to two of the tables.


# Getting started - details to follow...

Creating a free Airtable Account

Finding your account specific API documentation and API key

Creating a demo Base from an Airtable template

Using the Airtable API Encoder...

Adding columns to the relevant tables

Setting up Wordpress-Airtable users  - and permissions.

Adding the [my-airtable] shortcode to a page of your choosing.


# Known Limitations

At this time, within the plugin, there is no error handling for the calls to the airtable api.

# Further Assistance

stackoverflow
community.airtable.com



