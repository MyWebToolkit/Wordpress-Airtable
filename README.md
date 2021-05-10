# Wordpress-Airtable
Sample code for updating Airtable from Wordpress

# Purpose

This project provides a reference point for anyone seeking to integrate Wordpress with Airtable for the purpose of displaying Airtable data in Wordpress and, more importantly, update the Airtable data from Wordpress.

It is motivated by the fact that, as of the time of writing this, information, and examples, on how to update airtables from within wordpress, can be challenging to the first timer. It is not intended to be a definitive guide, but it is an approach that works and can be refined to your specific standards and needs.

# Approach

The project provides guidance on how to create a working proof of concept baseline of a Wordpress - Airtable integration, from which you can evolve a solution to meet your own requirements.

It is implemented as a Wordpress Plugin which uses teh Wordpress preferred method wp_remote_get() rather than directly using curl or javascript. The plugin, as is, can be configured to work with a free Airtable account and a standard Airtable Base template, with minor changes to two of the tables.


# Getting started

## Creating a free Airtable Account

If you already have an Airtable account, this step can be skipped.

If you don't already have an Airtable account, [Airtable](https://airtable.com/pricing) provide a free account option for 'individuals or teams just getting started with Airtable'.

Once you have signed up you will have access to an initial workspace usually named 'My First Workspace'. From here you can add a 'base' - Airtable jargon for a database.


## Creating a demo Base from an Airtable template

This plugin is designed to work with a specific Airtable Template 'Volunteer Management'. At the time of writing it can be found by choosing 'Add a base' from your workspace, choosing 'Start with a template'. The 'Volunteer Management' template is within the 'Nonprofit' category.

### Adding columns to the relevant tables

Once your base is created, you will need to add these specific columns to the following tables.

1) Volunteers: add a 'single line text' field type column titled 'username'
2) Volunteering Events: add a 'Date' field type column titled 'Completed'
3) Volunteering Events: add a 'Long Text' field type column titled 'Notes'

### Associating Wordpress usernames with Airtable Volunteers

Once you have added the 'username' column to the Volunteers table, you can invent usernames for some of the sample volunteers e.g. Mindy Patrick could be 'mindyp' and record them in the 'username' column.

Make a note of the 'usernames' as they will b e used to create usernames in Wordpress.


### Finding your account specific API documentation and API key

Once you have created your Base and added the columns as specified, your API Documentation will be ready for use. It is important to note that the documentation is dynamically generated to represent your base and tables. It can be found with the Help | <> API Documentation.

There are two ways to find your API key: - 

i) From within your Account Information when logged in.

ii) From within the API documentation there is a 'show API key' option in the top right corner of the code examples.

### Finding your API url

To get the API url, as required for configuring this plugin,  you can inspect the curl documentation and note the first part of the 'curl' upto but exculding `/Volunteers...` e.g.

`https://api.airtable.com/v0/appv0cudST3qFd34T` (this is a dummy one so don't try and use it!)

### Finding your Volunteers Table Name

For this plugin you will need the specific Airtable reference to the Volunteers Table. This can be found navigating to the 'VOLUNTEERS TABLE List records' section of the API documentation. There, under the heading 'List Volunteer records', there is a link to a, 'API URL encoder tool'. Follow that link and it will display the 'Base ID' and Table Name' for your specific base.

## Setting up Wordpress

The following instructions assume that you have a functioning Wordpress instance either on a local machine or in a hosted environment and have downloaded, installed and activated this plugin.

### Configuring the plugin

To configure the plugin you will require three pieces of information from Airtable:

1) Your Airtable API key
2) Your Airtable API Url
3) Your Volunteers Table Name

From with Admin | Tools there will be an option 'Airtable API' that will enable you to enter the three pieces of information.

Note: The API is not shown once entered and is encrypted within the database.

### Setting up Wordpress-Airtable users and permissions.

If you have not already invented and entered usernames for a selection of Volunteers in the 'username' column of the Airtable Volunteers table, do it now.

From within Wordpress create one or more users with an 'Author' role, assiging each new user one of the 'usernames' you assigned earlier within Airtable.

### Adding the [my-airtable] shortcode to a page of your choosing.

To generate the Airtable data within Wordpress, simply create a new Page, it can be titled anything you wish e.g. 'My Profile', and add the shortcode `[my-airtable]` to the page.

Publish the page.

### Testing

To test that everything is working and designed, sign into Wordpress as one of the Airtable volunteers - using a username (and password) you set up earlier, then navigate to the page you published. You should now see Airtable data, specifically 'Profile Details' and 'My Volunteering Events' (if the user is assigned to an event in Airtable). You will have the option to complete any listed events, addding a date and optionally completion notes. Once marked as completed the entered data will be added to the relevant event in Airtable. 


# Known Limitations

At this time, within the plugin, there is no error handling for the calls from wordpress to the airtable api.

# Further Assistance

Two sources I found useful were: 

[stackoverflow](https://stackoverflow.com)

[community.airtable.com](https://community.airtable.com)



