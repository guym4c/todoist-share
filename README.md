# todoist-share
A simple Slim app that generates a webpage showing what you're working on.

## Config
`config.php` contains the app settings. The app requires a `$todoistApiKey`, your personal API key for your Todoist account.  
`$ignore` is an array of project names that the app will ignore. These are useful for keeping personal todos hidden.
`$github` is your GitHub handle.

## GitHub integration
Where the app finds a reference to a GitHub issue, preceded by an asterisk, it will convert such a reference into a link to the issue on GitHub. For example, the todo `Write the Readme *todoist-share/1` will see `*todoist-share/1` replaced with a link to the issue. In the future, you will be able to specify a repo per Todoist project and [simply reference issues](https://github.com/guym4c/todoist-share/issues/1/) using the asterisk and issue number.
