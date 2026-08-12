# Form CSV

[![License](https://poser.pugx.org/t3brightside/formcsv/license)](LICENSE.txt)
[![Packagist](https://img.shields.io/packagist/v/t3brightside/formcsv.svg?style=flat)](https://packagist.org/packages/t3brightside/formcsv)
[![Downloads](https://poser.pugx.org/t3brightside/formcsv/downloads)](https://packagist.org/packages/t3brightside/formcsv)
[![Brightside](https://img.shields.io/badge/by-t3brightside.com-orange.svg?style=flat)](https://t3brightside.com)

**TYPO3 CMS extension providing a CSV finisher for the TYPO3 Form Framework.**  
Generates CSV files directly from form submissions and attaches them to outgoing emails.

## Features

- Generates a CSV file from form submission data in memory
- Attaches CSV files to receiver and/or sender emails
- Exclude specific form fields from the generated CSV output
- Configurable CSV output filename
- UTF-8 BOM support for seamless Excel compatibility

## Installation

- `composer req t3brightside/formcsv` or from TYPO3 extension repository **[formcsv](https://extensions.typo3.org/extension/formcsv/)**
- Include static TypoScript template or register the YAML configuration in your site package

## Usage

1. Open the **Form Editor** in the TYPO3 backend.
2. Add **CSV File Generator** (`CsvFinisher`).
3. Configure options:
    - **CSV Filename**: Set custom filename (default: `submission.csv`).
    - **Fields to Exclude**: Comma-separated list of element identifiers to omit (e.g. `{honeypot}, {captcha}`).
    - **Attach to Receiver Mail**: Toggle attachment for administrator/receiver email.
    - **Attach to Sender Mail**: Toggle attachment for user confirmation email.

## Sources

- [GitHub](https://github.com/t3brightside/formcsv)
- [Packagist](https://packagist.org/packages/t3brightside/formcsv)
- [TER](https://extensions.typo3.org/extension/formcsv/)

## Development & maintenance

[Brightside OÜ – TYPO3 development and hosting specialised web agency](https://t3brightside.com/)
