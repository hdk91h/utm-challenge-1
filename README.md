# nginx logs parser and analyzer

This application is a command-line tool developed to parse, analyze, and report on server access logs.

## Features

- **Stream processing** - able to handle large log files (500MB+), without memory limit.
- **Parsing** - Regex-based extraction of log format.
- **License Violation Detection** - Identifies software licenses used on multiple hardware.
- **Hardware analysis** - Counts active licenses per hardware/CPU.
- **PDF Reporting** - Generates a PDF report.

## Requirements

- PHP 8.0 or higher
- Composer
- PHP Extensions `ext-json`, `ext-zlib`

## Installation

1. **Clone the repository**
   ```bash
   git clone git@github.com:hdk91h/utm-challenge.git
   cd utm-challenge
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

## Usage
```bash
php app.php
```

The script will:

1. Read the log file from `data/access.log`
2. Display a summary of the analysis in the terminal
3. Generate a detailed PDF report in `output/report.pdf`

## Testing
The project includes unit tests for the core logic.

```bash
vendor/bin/phpunit tests
```


