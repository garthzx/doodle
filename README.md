# Doodle
##### A Google-like search engine written in PHP.

<br />
<div align="center">
  <a href="https://github.com/garthzx/doodle">
    <img src="assets/images/doodleLogo.png" alt="Logo" width="450px" height="135px">
  </a>
  <p align="center">
    <br />
    <br />
    <a href="https://github.com/garthzx/doodle/#Demo">View Demo</a>
    ·
    <a href="https://github.com/garthzx/doodle/issues">Report Bug</a>
    ·
    <a href="https://github.com/garthzx/doodle/issues">Request Feature</a>
  </p>
</div>

![Demo]("doodle/demo/demo.mp4")

> *Please note that can still be improved in many ways. I'm working on that.*

#### Description
Doodle is a simple search engine that is similar in features as Google's. It stores sites and images
in a MySQL database.

#### Motivation
The purpose of this project is to get to learn more about how search engines primarily work. It's also a step
up to learning PHP, databases, and frontend as well. 

#### How to run it on your local machine

##### Requirements
- PHP
- XAMPP

##### Steps
1. Go to localhost/phpmyadmin/ and create a database named 'doodle'
2. Import the .sql script found in the /sqlscripts directory
3. Move the project to C::\\Users\xampp\htdocs\
4. Go to your browser, navigate to localhost/[project-name]

##### Configuration
If you have a different port in use for phpmyadmin, you can configure it in `/config.php` by
changing the port value in the connection string:

```js
  $conn = new PDO("mysql:dbname=doodle;host=localhost;port=3306", "root", "");
```

## Contact
Garth - [Facebook](https://www.facebook.com/garthzx/) - [Instagram](https://www.instagram.com/garthzx/)