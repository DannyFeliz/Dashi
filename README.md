# [Dashi](https://github.com/DannyFeliz/Dashi)
Get a notification in Slack every time someone asks you to check some code on Github or Bitbucket.

<p align="center">
  <img width="350" src="http://dashinotify.com/img/dashi-logo.png">
</p>

# Screenshots
#### Request review
<img src="https://raw.githubusercontent.com/DannyFeliz/Dashi/master/public/img/screenshot/request-review-pr-example.png">

#### Request Changes
<img src="https://raw.githubusercontent.com/DannyFeliz/Dashi/master/public/img/screenshot/request-changes-example.png">

#### Mention in comment
<img src="https://raw.githubusercontent.com/DannyFeliz/Dashi/master/public/img/screenshot/mention-in-comment-example.png">


# Support
<table>
    <tr>
        <th>Feature</th>
        <th>Github</th>
        <th>Bitcket</th>
    </tr>
    <tr>
        <td>Request Review in a pull request</td>
        <td>:heavy_check_mark:</td>
        <td>:heavy_check_mark:</td>
    </tr>
    <tr>
        <td>Request Changes in a pull request</td>
        <td>:heavy_check_mark:</td>
        <td></td>
    </tr>
    <tr>
        <td>Mentions in comments</td>
        <td>:heavy_check_mark:</td>
        <td></td>
    </tr>
</table>

# Webhooks needed
<table>
    <tr>
        <th>Event</th>
        <th>Github Webhook</th>
        <th>Bitbucket Webhook</th>
    </tr>
    <tr>
        <td>Request Review in a pull request</td>
        <td>Pull request</td>
        <td>Created</td>
    </tr>
    <tr>
        <td>Request Changes in a pull request</td>
        <td>Pull request</td>
        <td></td>
    </tr>
    <tr>
        <td>Mentions in comments</td>
        <td>Pull request review comment</td>
        <td></td>
    </tr>
</table>

# Setup

> ### Generate a [Slack Incoming WebHooks](https://devsop.slack.com/apps/A0F7XDUAZ-incoming-webhooks)
![webhook](https://i.imgur.com/BROWDw2.png)
![webhook-2](https://i.imgur.com/FGxZY9e.png)
![webhook-3](https://i.imgur.com/NpF7sFh.png)

<hr>

> ### Setup your info in [Dashi](http://dashinotify.com)
1. Go to [Dashi](http://dashinotify.com/register) and login o register
2. Type your Github or Bitbucket username 
3. Paste the copied Slack Webook URL
4. Save :)

<hr>

> # Setup your Repository
Use [http://dashinotify.com/notifier](http://dashinotify.com/notifier) as the URL of the WebHook.

> ### Github
![bitbucket](https://i.imgur.com/mE2sPWX.png)
![bitbucket](https://i.imgur.com/sVjmRdY.png)
![bitbucket](https://i.imgur.com/oeXbrCL.png)

<hr>

> ### Bitbucket
![bitbucket](https://i.imgur.com/7GwB4LX.png)
