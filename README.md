# E-Commerce Application

## Overview

This repository contains the backend code for an e-commerce application built with Laravel. It includes services, controllers, repositories, and requests to manage the core functionalities such as orders, products, and cart operations.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Code Structure](#code-structure)
  - [Services](#services)
  - [Repositories](#repositories)
  - [Controllers](#controllers)
  - [Requests](#requests)
  - [Resources](#resources)
- [API Endpoints](#api-endpoints)
- [Error Handling](#error-handling)
- [License](#license)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/muhammad-anas-darwish/test-e-commerce.git
   ```

2. Navigate to the project directory:
   ```bash
   cd e-commerce
   ```

3. Install dependencies using Composer:
   ```bash
   composer install
   ```

4. Copy the .env.example to .env and configure your environment variables:
   ```bash
   cp .env.example .env
   ```

5. Generate an application key:
   ```bash
   php artisan key:generate
   ```

6. Run migrations to set up the database:
   ```bash
   php artisan migrate
   ```
