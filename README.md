# CliXX Retail Application - Docker Image Pipeline

## Overview

This repository contains the **CliXX Retail WordPress Application** and its associated **CI/CD Docker Image Pipeline**. The pipeline automates the process of building, testing, and deploying containerized WordPress applications to AWS Elastic Container Service (ECS).

---

## Pipeline Architecture

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  SonarQube  │───▶│   Docker    │───▶│   Ansible   │───▶│  AWS ECR    │
│  Code Scan  │    │   Build     │    │  DB Setup   │    │  Push       │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
                                                                │
                                                                ▼
                                                         ┌─────────────┐
                                                         │  ECS/Fargate│
                                                         │  Deployment │
                                                         └─────────────┘
```

---

## What This Pipeline Does

### 1. **Code Quality Analysis (SonarQube)**
- Scans PHP/WordPress code for bugs, vulnerabilities, and code smells
- Enforces quality gates before allowing deployment
- Excludes WordPress core files (wp-admin, wp-includes) to focus on custom code

### 2. **Docker Image Build**
- Creates a containerized WordPress application using PHP 7.4 with Apache
- Installs required PHP extensions (mysqli, pdo, gd, zip)
- Packages application code into a portable, reproducible image

### 3. **Database Provisioning (Ansible)**
- Automatically restores RDS MySQL instance from a snapshot
- Configures WordPress database URLs for the target environment
- Handles cross-account AWS role assumption for secure access

### 4. **Container Registry Push (ECR)**
- Tags images with version numbers and `latest`
- Pushes to AWS Elastic Container Registry for deployment
- Supports cross-account deployments via IAM role assumption

---

## Purpose

The primary purpose of this pipeline is to:

| Goal | Description |
|------|-------------|
| **Automation** | Eliminate manual deployment steps, reducing human error |
| **Consistency** | Ensure identical environments across dev, staging, and production |
| **Speed** | Deploy new versions in minutes instead of hours |
| **Reliability** | Automated testing and quality gates prevent bad code from reaching production |
| **Scalability** | Container-based deployment enables horizontal scaling on ECS |

---

## Industry Use Cases

### E-Commerce Platforms
- Retail companies use similar pipelines to deploy WordPress/WooCommerce stores
- Black Friday/Cyber Monday traffic spikes require rapid scaling via containers

### Media & Publishing
- News websites with WordPress backends use containerized deployments
- Enables blue-green deployments for zero-downtime updates

### Enterprise WordPress
- Large organizations run WordPress as a headless CMS
- Containers provide isolation between different business units' sites

### SaaS Providers
- Companies offering managed WordPress hosting use container orchestration
- Each customer gets an isolated container instance

---

## Ansible: Infrastructure as Code

### What is Ansible?

**Ansible** is an open-source automation tool used for:
- **Configuration Management** - Define server states declaratively
- **Application Deployment** - Deploy apps across multiple servers
- **Cloud Provisioning** - Create and manage cloud resources (AWS, Azure, GCP)
- **Orchestration** - Coordinate multi-step workflows

### How Ansible is Used in This Pipeline

```yaml
# deploy_db_ansible/deploy_db.yml
- Assumes cross-account IAM role for secure AWS access
- Restores RDS MySQL instance from a snapshot
- Waits for database to become available
- Returns database endpoint for application configuration
```

### Why Ansible?

| Feature | Benefit |
|---------|---------|
| **Agentless** | No software to install on target systems - uses SSH/APIs |
| **Idempotent** | Running the same playbook multiple times produces the same result |
| **Human-Readable** | YAML syntax is easy to understand and maintain |
| **AWS Integration** | Native modules for RDS, EC2, S3, and other AWS services |

---

## Docker Entrypoint Modification

### The Problem (Before)

Previously, WordPress URL updates were handled in **EC2 user data scripts**:

```bash
# OLD APPROACH - EC2 User Data
#!/bin/bash
yum install -y mysql
mysql -h $RDS_HOST -u $DB_USER -p$DB_PASS -D $DB_NAME -e \
  "UPDATE wp_options SET option_value = '$SITE_URL' WHERE option_name IN ('siteurl', 'home');"
```

**Issues with this approach:**
- ❌ Runs once when EC2 instance launches, not when container starts
- ❌ If RDS isn't ready, the update fails silently
- ❌ New container deployments don't trigger the update
- ❌ EC2 instance restarts don't re-run user data

### The Solution (After)

The database URL update now happens in the **Docker entrypoint script**:

```bash
# NEW APPROACH - docker-entrypoint.sh
#!/bin/bash
# Wait for database, update WordPress URLs, then start Apache
mysql -h "$DB_HOST" ... -e "UPDATE wp_options SET option_value = '$SITE_URL' ..."
exec apache2-foreground
```

**Benefits of this approach:**

| Benefit | Description |
|---------|-------------|
| ✅ **Container-Level** | Runs every time a container starts, not just on EC2 launch |
| ✅ **Retry Logic** | Waits up to 5 minutes for RDS to become available |
| ✅ **Visibility** | Logs are captured in CloudWatch via ECS task logging |
| ✅ **Portability** | Works the same whether running locally or on ECS |
| ✅ **Self-Healing** | Container restarts automatically fix URL issues |

### Entrypoint Flow

```
Container Starts
       │
       ▼
┌──────────────────┐
│ Wait for RDS     │◀──┐
│ (retry 30x)      │   │ 10 sec
└────────┬─────────┘   │
         │ success     │
         ▼             │
┌──────────────────┐   │
│ Update wp_options│───┘
│ siteurl & home   │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Start Apache     │
│ (apache2-foreground)
└──────────────────┘
```

---

## File Structure

```
html/
├── Dockerfile                 # Container build instructions
├── docker-entrypoint.sh       # Custom entrypoint for URL updates
├── Jenkinsfile-1              # CI/CD pipeline definition
├── wp-config.php              # WordPress database configuration
├── deploy_db_ansible/
│   ├── deploy_db.yml          # Ansible playbook to restore RDS
│   └── delete_db.yml          # Ansible playbook to delete RDS
├── wp-admin/                  # WordPress admin (excluded from scan)
├── wp-includes/               # WordPress core (excluded from scan)
└── wp-content/                # Themes, plugins, uploads
```

---

## Quick Start

### Prerequisites
- Jenkins with Docker and Ansible installed
- AWS credentials with ECR and RDS access
- SonarQube server for code analysis

### Running the Pipeline

1. **Trigger Jenkins Pipeline**
   ```
   Jenkins Dashboard → CliXX Pipeline → Build Now
   ```

2. **Approve Manual Gates**
   - Confirm "Tear Down Environment?" after testing
   - Confirm "Push Image To ECR?" to deploy

3. **Deploy to ECS**
   ```bash
   cd /path/to/terraform
   terraform apply
   ```

---

## Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `DB_HOST` | RDS MySQL endpoint | `wordpressdbclixxjenkins.xxx.rds.amazonaws.com` |
| `DB_USER` | Database username | `wordpressuser` |
| `DB_PASS` | Database password | `W3lcome123` |
| `DB_NAME` | Database name | `wordpressdb` |
| `SITE_URL` | WordPress site URL | `http://ecs.stack-claye.com` |

---

## Author

**Richard Claye**  
StackCloud13 Team  
Contact: richard.claye@gmail.com

---

## License

This project is for educational and demonstration purposes.
