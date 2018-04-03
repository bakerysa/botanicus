# WPGulp Setup

## Description

This project uses [WPGulp](https://github.com/ahmadawais/WPGulp)

This is a wordpress-based project. This means that all of the installation and configuration files for Wordpress are ommited and only the `wp-content` directory is tracked. This includes all of the theme and plugin files. In order to get this project up and running, simply drop the theme folder into a Wordpress installation thus replacing the entire content folder. For modification of the theme follow the steps below.

## Getting Started

1. Navigate to the theme folder.
2. Edit the project variables in the `gulpfile.js` between the two comments `// START Editing Project Variables.` and `// STOP Editing Project Variables.` to match your dev environement.
3. In theme root using terminal run the following commands: `sudo npm install --global gulp` and then `sudo npm install`
4. To start the server simply run `gulp`.

### Note

No database / sql file is included for security purposes. This can be retrieved from the server.
