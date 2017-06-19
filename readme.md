# Uptyme Checker

Uptyme is a PHP CLI script to verify the uptime of websites.

## Why is it spelled Uptyme, you code hipster?

[`uptime`](https://linux.die.net/man/1/uptime) is a Linux command for checking system uptime. We obviously could not use this name!

## Installation

```
composer global require gigasavvy/uptyme
```

## Usage

Uptyme should be run from the command line and can be used in various ways.

### Basic Checking

To check that a single website is up, use the following command:

```
$ uptyme run https://gigasavvy.com
Checking HTTPS integrity of the given domains...
Checker completed with 0 failed domains.
```

To check that multiple websites are up, simply pass additional domains as arguments:

```
$ uptyme run https://gigasavvy.com https://google.com https://php.net
Checking HTTPS integrity of the given domains...
Checker completed with 0 failed domains.
```

### Logging

To enable a log (useful for cron-based running), pass the log file to the `--log` option:

```
$ uptyme run https://gigasavvy.com --log /var/log/uptyme.log
Checking HTTPS integrity of the given domains...
Checker completed with 0 failed domains.
Check logs for more info (/var/log/uptyme.log)
```

### Slack Integration

For any failed websites, you can be alerted via Slack by passing your Slack webhook url to the `--slack` option:

```
$ uptyme run https://foo.bar --slack https://hooks.slack.com/services/123/456
Checking HTTPS integrity of the given domains...
Checker completed with 1 failed domains.
Failed domains:
https://foo.bar
```

As we can see, the site `https://foo.bar` failed to connect. By passing the `--slack` option, your Slack channel will also be notified.
