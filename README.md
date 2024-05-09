# Nurse Roster System

## Overview
The Nurse Roster System is designed to automate the scheduling of nursing shifts for a hospital environment, ensuring that nurse assignments meet specific operational and HR requirements.

## Features

### Roster Building Algorithm
- **Dynamic Nurse Scheduling**: Supports scheduling where no nurse is assigned to more than one shift per day.
- **Minimum Staff Requirements**: Ensures each shift is filled with exactly five nurses, utilizing a rotating system to maintain fairness and prevent fatigue.
- **Error Handling**: If there are fewer than 15 nurses available, which is the minimum to fill all shifts in a day without overlap, the system throws an exception to prevent roster generation.

### PDF Generation
- **Automatic PDF Creation**: Automatically generates a PDF containing the weekly nurse roster.
- **Formatted Output**: The PDF includes well-formatted tables displaying the nurse schedule, with clear distinctions between morning, evening, and night shifts for each day within a specified period.
- **File Handling**: The system stores the PDFs in a designated directory and can automatically open them upon generation depending on the operating system.


### Testing
- **Unit Tests**: Extensive unit testing for both the roster generation and PDF output functionalities, ensuring that the system behaves as expected under various conditions.
- **Feature Tests**: Includes tests for PDF generation to verify file creation and storage.
- **Edge Cases**: Tests cover scenarios such as insufficient nurse numbers and rotation effectiveness to ensure robust system performance.


## Setup and Usage
To set up the Nurse Roster System, ensure you have Composer and PHP installed on your system. Install the necessary dependencies by running:

add .env file
```bash
composer install
php artisan test
php artisan app:generate-roster 'sample_data/nurses.json' {start-date} {end-date}
