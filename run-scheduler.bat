@echo off
REM Laravel Task Scheduler Runner
REM This file runs Laravel's schedule:run command
REM Set up Windows Task Scheduler to run this file every minute

cd /d C:\xampp\htdocs\hrm_rising
C:\xampp\php\php.exe artisan schedule:run >> C:\xampp\htdocs\hrm_rising\storage\logs\scheduler.log 2>&1
