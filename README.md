# Energy Management System (EnMS) for Optimise Sdn. Bhd.

[![E2E Tests](https://github.com/Mohamedarshathsaleem/FYP_Optimise/actions/workflows/e2e.yml/badge.svg)](https://github.com/Mohamedarshathsaleem/FYP_Optimise/actions/workflows/e2e.yml)
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)
![Playwright](https://img.shields.io/badge/tested_with-Playwright-2EAD33?logo=playwright&logoColor=white)

**An ISO 50001 energy-management platform that replaced an energy consultancy's spreadsheet workflow. It detects Significant Energy Uses automatically, builds regression baselines and gives 5 user roles their own dashboards.**

<!-- TODO(Arshath): add the live URL once deployed (render.yaml and railway.toml are ready): -->
<!-- 🔗 **Live demo:** https://fyp-optimise.onrender.com -->

<!-- TODO(Arshath): add 2–3 screenshots (EnPI dashboard, SEU flagging, baseline regression) to docs/ and uncomment: -->
<!-- <p align="center"><img src="docs/dashboard.png" width="45%"> <img src="docs/seu.png" width="45%"></p> -->

## 👤 My role

Solo final-year project, built during my part-time role at Optimise Systems Engineering. I owned the full SDLC: requirements gathering with the client's energy managers, UML/ERD design, Laravel implementation, an RBAC permission system, the Playwright E2E suite with GitHub Actions CI, and UAT with end users.

> Final Year Project — Bachelor of Computer Science (Software Engineering) with Honours  
> Faculty of Computing, Universiti Teknologi Malaysia (UTM)  
> Author: Mohamed Arshath Bin Mohamed Saleem (A22EC0076)  
> Supervisor: Puan Lizawati Binti Mi Yusuf | January 2026

---

## Overview

A web-based **Energy Management System (EnMS)** developed in collaboration with **Optimise Systems Engineering Sdn. Bhd.**, a Malaysian energy management consultancy and registered ESCO under the Malaysian Energy Commission.

The system automates the **PLAN phase of the ISO 50001 PDCA cycle**, replacing error-prone manual spreadsheet workflows with a centralized digital platform for energy data management, energy review, Significant Energy Use (SEU) identification, and regression-based baseline performance evaluation.

---

## Problem Statement

Optimise Sdn. Bhd. relied on manual, spreadsheet-based processes for:

- Collecting and consolidating energy consumption data (electricity, fuel, water)
- Calculating Specific Energy Consumption (SEC) and Energy Intensity Performance (EIP)
- Identifying Significant Energy Uses (SEUs)
- Building and maintaining baseline performance models

This caused **data discrepancies, calculation errors, slow workflows**, and difficulty maintaining ISO 50001-compliant documentation.

---

## Project Objectives

1. **Identify** the requirements of the EnMS supporting energy data management, energy review, and performance baseline analysis in compliance with ISO 50001 standards.
2. **Design and develop** a web-based EnMS incorporating data processing, SEU identification, and baseline performance evaluation based on Optimise Sdn. Bhd.'s operational needs.
3. **Test, verify, and validate** system functionalities to ensure accuracy, ISO 50001 compliance, and operational efficiency.

---

## Key Features

### 🗂️ Energy Data Management Module
- Centralized logging of energy consumption data (electricity, fuel, water) across departments
- Management of production data, operational variables, and energy resource records
- Built-in data validation to prevent duplicate or inconsistent entries
- Linking of energy consumption to production performance metrics

### 📊 Energy Review & SEU Identification Module
- Automated calculation of **Specific Energy Consumption (SEC)** and **Energy Intensity Performance (EIP)**
- Sequential load apportionment across departments (mineral, steel, cement industries)
- Automated **SEU flagging** based on configurable threshold criteria
- ISO 50001-compliant energy review workflows

### 📈 EnPI & Baseline Management Module
- Regression-based baseline modeling with statistical validation (R² values, residual analysis)
- Comparison of actual energy performance against established baselines
- Automated baseline and EnPI updates when new data is incorporated
- Audit-ready documentation for ISO 50001 compliance

### 🔐 Role-Based Access Control (RBAC)
Five distinct user roles with differentiated permissions:

| Role | Description |
|---|---|
| Superadmin | Full system administration |
| Top Management | Strategic dashboard and summary reports |
| Energy Management Team | Data entry, validation, and reporting |
| Internal REM | Baseline analysis and performance evaluation |
| External REM | SEU verification and EnPI review |

### 📱 Responsive Design
Accessible across desktops, laptops, tablets, and smartphones.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend Framework | Laravel (PHP) |
| Database | MySQL |
| Frontend | HTML5, CSS3, JavaScript |
| Architecture | 3-Tier (MVC-based) |
| Design Pattern | MVC (Model-View-Controller) |

---

## System Architecture

The system follows a **3-Tier Architecture**:
- **Presentation Layer** — HTML5/CSS3/JavaScript frontend
- **Application Layer** — Laravel MVC backend with business logic
- **Data Layer** — MySQL relational database

---

## Development Methodology

The system was developed using the **Waterfall Model**, progressing through:

1. Requirements Gathering & Analysis
2. System Design (UML diagrams, ERD, interface mockups)
3. Implementation
4. Testing (Functional, Integration, User Acceptance)
5. Deployment
6. Maintenance

---

## Getting Started

**Prerequisites:** PHP 8.3, Composer, Node.js 20, MySQL 8.

```bash
git clone https://github.com/Mohamedarshathsaleem/FYP_Optimise.git
cd FYP_Optimise

composer install
npm ci && npm run build

cp env.example .env          # set DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan key:generate
php artisan migrate --seed   # creates the schema, reference data and one demo user per role

php artisan serve            # http://127.0.0.1:8000
```

Run the end-to-end tests (with the app running):

```bash
npx playwright install chromium
npm run test:e2e
```

**Deployment:** `Dockerfile`, `render.yaml` (Render, Singapore region, managed Postgres) and `railway.toml` are included for one-click hosting.

---

## Testing

**Automated:** 17 Playwright end-to-end tests cover authentication, role-based access control, dashboards and public pages. GitHub Actions runs them on every push against a fresh MySQL 8 database (see `.github/workflows/e2e.yml`).

The system was also validated through three levels of manual testing:

- **Functional Testing** — Verifies each feature works correctly in isolation
- **Integration Testing** — Ensures modules work together as expected
- **User Acceptance Testing (UAT)** — Confirms the system meets end-user requirements

---

## Project Scope

- Covers only the **PLAN phase** of the ISO 50001 PDCA cycle
- Designed specifically for Optimise Systems Engineering Sdn. Bhd.'s operational context
- Supports industries including mineral, steel, and cement processing
- Standalone platform; external system integration can be added in future iterations

---

## Abbreviations

| Term | Meaning |
|---|---|
| EnMS | Energy Management System |
| EnPI | Energy Performance Indicator |
| EIP | Energy Intensity Performance |
| ESCO | Energy Services Company |
| ISO | International Organization for Standardization |
| PDCA | Plan-Do-Check-Act |
| RBAC | Role-Based Access Control |
| REM | Registered Energy Manager |
| SEC | Specific Energy Consumption |
| SEU | Significant Energy Use |
| UTM | Universiti Teknologi Malaysia |

---

## Academic Information

| Field | Detail |
|---|---|
| Institution | Universiti Teknologi Malaysia (UTM) |
| Faculty | Faculty of Computing |
| Programme | Bachelor of Computer Science (Software Engineering) with Honours |
| Matric No. | A22EC0076 |
| Academic Session | 2025/2026-2 |
| Supervisor | Puan Lizawati Binti Mi Yusuf |
| Submission Date | January 2026 |

---

## Related Systems Analyzed

The following existing EnMS platforms were reviewed during the literature study:

- **Enerit** — ISO 50001-compliant EnMS with SEU documentation and dashboards
- **Process.st** — Workflow automation platform for checklist and approval management
- **Tor.ai** — AI-powered industrial analytics platform with IoT integration

---

> This project was submitted in fulfilment of the requirements for the Bachelor of Computer Science (Software Engineering) with Honours degree at Universiti Teknologi Malaysia.

---

## License

Developed for Optimise Systems Engineering Sdn. Bhd. as an academic project. The source is shared publicly for portfolio and assessment purposes only. All rights reserved.
