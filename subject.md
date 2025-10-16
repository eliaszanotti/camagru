# Camagru

## Summary
The goal of this project is to build a web application.

## Version
4

## Objectives

This web project is challenging you to create a small web application allowing you to make basic photo and video editing using your webcam and some predefined images.

Obviously, these images should have an alpha channel, otherwise your superposition would not have the expected effect!

We will, for instance, picture the precise moment of an intergalactical cat launch, here's the evidence:

App's users should be able to select an image in a list of superposable images (for instance a picture frame, or other "we don't wanna know what you are using this for" objects), take a picture with his/her webcam and admire the result that should be mixing both pictures.

All captured images should be public, likeables and commentable.

## General instructions

- This project will be corrected by humans only. You're allowed to organise and name your files as you see fit, but you must follow the following rules.
- Your web application must produce no errors, no warning or log line in any console, server side and client side. Nonetheless, due to the lack of HTTPS, any error related to getUserMedia() are tolerated.
- You are free to use any language to create your server-side application, but, for every function that you use you must check that an equivalent exist in PHP standard library.
- Client-side, your pages must use HTML, CSS and JavaScript.
- Up to date containerization is a must.
- Remember that some choice can make you more attractive on the job market.
- Every framework, micro-framework or library that you don't create and without an equivalent in PHP standard library are totally forbidden, except for CSS frameworks that doesn't need forbidden JavaScript.
- Your application must be free of any security leak. You must handle at least cases mentioned in the mandatory part. Nonetheless, you are encouraged to go deeper into your application's safety, think about your data's privacy!
- You are free to use any webserver you want, like Apache, Nginx or even the built-in webserver.
- Your web application should be at least be compatible with Firefox (>= 41) and Chrome (>= 46).

For obvious security reasons, any credentials, API keys, env variables etc... must be saved locally in a .env file and ignored by git. Publicly stored credentials will lead you directly to a failure of the project.

## Mandatory Part

### Common features

You will develop a web application. Even if this is not required, try to structure your application (as a MVC application, for instance).

Your website should have a decent page layout (meaning at least a header, a main section and a footer), be able to display correctly on mobile devices and have an adapted layout on small resolutions.

All your forms should have correct validations and the whole site should be secured.

This point is MANDATORY and shall be verified when your application would be evaluated. To have an idea, here are some stuff that is NOT considered as SECURE:
- Store plain or unencrypted passwords in the database.
- Offer the ability to inject HTML or "user" JavaScript in badly protected variables.
- Offer the ability to upload unwanted content on the server.
- Offer the possibility of altering an SQL query.
- Use an extern form to manipulate so-called private data

### User features

- The application should allow a user to sign up by asking at least a valid email address, an username and a password with at least a minimum level of complexity.
- At the end of the registration process, an user should confirm his account via a unique link sent at the email address filled in the registration form.
- The user should then be able to connect to your application, using his username and his password. He also should be able to tell the application to send a password reinitialisation mail, if he forget his password.
- The user should be able to disconnect in one click at any time on any page.
- Once connected, an user should modify his username, mail address or password.

### Gallery features

- This part is to be public and must display all the images edited by all the users, ordered by date of creation. It should also allow (only) a connected user to like them and/or comment them.
- When an image receives a new comment, the author of the image should be notified by email. This preference must be set as true by default but can be deactivated in user's preferences.
- The list of images must be paginated, with at least 5 elements per page.

### Editing features

This part should be accessible only to users who are authenticated/connected and should politely reject all other users who attempt to access it without being successfully logged in.

This page should contain 2 sections:
- A main section containing the preview of the user's webcam, the list of superposable images and a button allowing to capture a picture.
- A side section displaying thumbnails of all previous pictures taken.

- Superposable images must be selectable and the button allowing to take the picture should be inactive (not clickable) as long as no superposable image has been selected.
- The creation of the final image (so among others the superposing of the two images) must be done on the server side.
- Because not everyone has a webcam, you should allow the upload of a user image instead of capturing one with the webcam.
- The user should be able to delete his edited images, but only his, not other users' creations.

### Constraints and Mandatory things

To sum up things, your Über application should respect the following technologic choices:
- Authorized languages:
  - [Server] Any (limited to PHP standard library)
  - [Client] HTML - CSS - JavaScript (only with browser natives API)
- Authorized frameworks:
  - [Server] Any (up to PHP standard library)
  - [Client] CSS Frameworks tolerated, unless it adds forbidden JavaScript.

Your project must imperatively contain:
- One (or more) container to deploy your site with one command. anything equivalent to docker-compose is ok.

## Bonus part

If the required part is done entirely and perfectly, you can add any bonus you wish; They will be evaluated by your reviewers. You should however still respect the requirements in the bonus parts (i.e. image processing should be done on server side).

If you lack inspiration, here are some leads:
- "AJAXify" exchanges with the server.
- Propose a live preview of the edited result, directly on the webcam preview. We should note that this is much easier than it looks.
- Do an infinite pagination of the gallery part of the site.
- Offer the possibility to a user to share his images on social networks.
- Render an animated GIF.

The bonus part will only be assessed if the mandatory part is PERFECT. Perfect means the mandatory part has been integrally done and works without malfunctioning. If you have not passed ALL the mandatory requirements, your bonus part will not be evaluated at all.

## Submission and peer-evaluation

Turn in your assignment in your Git repository as usual. Only the work inside your repository will be evaluated during the defense. Don't hesitate to double check the names of your folders and files to ensure they are correct.

---

## TODO List

### Common Features
- [ ] Structure application as MVC (recommended)
- [ ] Create decent page layout (header, main section, footer)
- [ ] Implement responsive design for mobile devices
- [ ] Add adapted layout for small resolutions
- [ ] Implement form validations
- [ ] Ensure application security (no XSS, SQL injection, etc.)
- [ ] Set up containerization (Docker/docker-compose)

### User Features
- [ ] Create user registration form (email, username, password)
- [ ] Implement email validation for account confirmation
- [ ] Create login/authentication system
- [ ] Implement password reset functionality
- [ ] Add logout functionality
- [ ] Create user profile editing (username, email, password)
- [ ] Implement password complexity requirements

### Gallery Features
- [ ] Create public gallery page
- [ ] Display all images ordered by creation date
- [ ] Implement like functionality for authenticated users
- [ ] Implement comment functionality for authenticated users
- [ ] Add email notification for new comments (default enabled)
- [ ] Create user preferences to disable comment notifications
- [ ] Implement pagination (at least 5 images per page)

### Editing Features
- [ ] Create authenticated-only editing page
- [ ] Implement webcam preview functionality
- [ ] Create list of superposable images
- [ ] Implement image selection functionality
- [ ] Create capture button (disabled until image selected)
- [ ] Implement server-side image processing/superposition
- [ ] Add image upload alternative to webcam
- [ ] Create thumbnail gallery of user's previous pictures
- [ ] Implement delete functionality for user's own images

### Database Setup
- [ ] Design database schema (users, images, likes, comments)
- [ ] Create migration scripts
- [ ] Set up database connection

### Security Requirements
- [ ] Implement password hashing (no plain passwords)
- [ ] Prevent XSS attacks
- [ ] Prevent SQL injection
- [ ] Implement CSRF protection
- [ ] Validate file uploads
- [ ] Secure environment variables (.env file)

### Testing & Deployment
- [ ] Test application in Firefox (>= 41)
- [ ] Test application in Chrome (>= 46)
- [ ] Ensure no console errors/warnings
- [ ] Test all form validations
- [ ] Test email functionality
- [ ] Create deployment documentation