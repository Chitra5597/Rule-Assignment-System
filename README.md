# Rule-Assignment-System

A web-based hierarchical rule assignment management system with Vue.js frontend and PHP 8.0+ backend, enabling organizations to create, manage, and visualize complex rule hierarchies with built-in constraint enforcement.

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://www.php.net)
[![Vue.js Version](https://img.shields.io/badge/Vue.js-3.0%2B-green.svg)](https://vuejs.org)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0%2B-blue.svg)](https://www.mysql.com)

## 📌 Problem Statement

Organizations require a system to manage complex hierarchical rule structures where:
- Rules are organized into logical groups
- Rules follow a Condition-Decision branching model
- Hierarchies are limited to maximum 3 tiers
- Condition rules must have at least one child
- Decision rules cannot have children
- Rules can be reused across tiers but not under the same parent

This system provides a complete solution for creating, managing, and visualizing such hierarchies with comprehensive validation and data integrity.

**Full Problem Statement**: See [problem-statement.md](problem-statement.md)

---

## Solution Report

Implemented the full proof solution with Database design and Rest API with frontend web interface for complex hierarchical rule structures system.
- Designed database entities with ERD
- Added migration script and database tables
- Created PHP backend with Rest API
- Generated vue.js web application interface for Rule Management system.
- Generated solutioning report
- Added References and Issues occurred with Screenshots

**Full Problem Solution**: See [solution-report.md](solution-report.md)
---
