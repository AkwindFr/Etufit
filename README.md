# "How to use" is under descriptions see below.

# Etufit Project

Hello and welcome to Etufit project.

This application is based on :
- Symfony 4.x
- Materialize 1.0
- Library fullcalendar 3.10

Please find here the specifications (french only), don't hesitate to comment : https://docs.google.com/document/d/1twMOsR0S8uHUe0VzIGInCyrRym-1R7Ca5fjONAndH3E/edit?usp=sharing

please find here our trello dashboard if you want to work on this project with us (no obligations). You are welcome to join the group and contribute to this project:
https://trello.com/b/qPHf09vy/etufit

# WhoIs

Etufit is an end Training (internship) project intended to Bourgogne University (France), it's also the subject of our next graduation in may 2019.

This application is build to replace the actual reservation system of the new fitness who is managed with paper now.

# Why we share this project ?

Etufit has the possibility to be declined in different kind of way, actually we work on reservation and agenda system for fitness room. Feel free to developp another way to use it. Reservation of rooms, offices, materials etc...
We have no obligation to keep private, it's an opportunitie for our team to share it with the whole world and find inspiration, advises, and maybe someone like you, who can help us to build a better, faster, stronger applications.

Etufit is an occasion to learn and improve our skills by working on a real project.

# Description

Etufit is an application built with symfoni 4.x, materialize 1.x and full calendar 3.x.
Please find in specfications the structure of etufit 0.1

You can find :

User system : sign up / sign in / sign out
administrator system : superadmin / admin / referent / user

superAdmin :
    - Must be created by devs
    - Cannot be suppressed
    - has the right to manage admins.
    - has all the admin rights (see admin)

admin :
    - Named by Superadmin
    - has the right to manage Referents and users.
    - has the right to manage openings and assign referent
    - Has all the referent and users rights

Referent :
    - Named by admins
    - Has the right to see wich user has reserved opening (list in opening modal)
    - has all users rights

User :
    - Default status after inscription
    - has the right to make or cancel reservations
    - has access to is own historic
    - has access to his profil page


# How to use

Etufit is a symfony project.

Make sure that composer is installed on your computer if you want to use Etufit.
Link to composer : https://getcomposer.org/
Restart your computer after installing.
In some case you can have missing php env var error when you try to use composer directly without restarting your computer after installation.

- Create folder on your computer.
- Create git repository.
- Clone etufit project inside.
- Use composer install command in the folder
- Find .env file in etufit folder
    - make a copy and rename it : .env.local
    /!\ You musn't modify .env orignal file, always work on .env.local, who will be ignored by gitignore file /!\
- Etufit works with google recaptcha v2 /!\
    Add your private and public key for recaptcha in .env.local file
- Change your access to your database (line 18)
- Follow those steps to create and save dataBase :
    - open command in etufit folder
    - php bin/console doctrine:database:create etufit
    then
    - php bin/console make:migration
    then
    - php bin/console doctrine:migrations:migrate

You are now ready to use Etufit. See you soon !

# Contributing
- Don't forget to be clear with your commit, help us and yourself contribute and work on your comments !
- You can comment in french or in english, as you wish.
- Fork it
- Create your feature branch (git checkout -b my-new-feature)
- Commit your changes (git commit -am 'Add some feature')
- Push to the branch (git push origin my-new-feature)
- Create new Pull Request
