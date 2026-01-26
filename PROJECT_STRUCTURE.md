# ISP Billing System - Project Structure

## 🎯 Overview

Aplikasi Billing ISP multi-role dengan 4 role berbeda: Super Admin, ISP Admin, Technician, dan Customer.

## 🏗️ Architecture

### Backend (Laravel 11)

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── API/
│   │   │   │   ├── Auth/
│   │   │   │   │   ├── LoginController.php
│   │   │   │   │   ├── RegisterController.php
│   │   │   │   │   └── EmailVerificationController.php
│   │   │   │   ├── SuperAdmin/
│   │   │   │   │   ├── ISPAdminController.php
│   │   │   │   │   ├── PackageController.php
│   │   │   │   │   ├── PaymentGatewayController.php
│   │   │   │   │   └── WebCustomizationController.php
│   │   │   │   ├── ISPAdmin/
│   │   │   │   │   ├── CustomerController.php
│   │   │   │   │   ├── PackageAssignmentController.php
│   │   │   │   │   ├── MikrotikController.php
│   │   │   │   │   ├── TechnicianController.php
│   │   │   │   │   └── PaymentApprovalController.php
│   │   │   │   ├── Technician/
│   │   │   │   │   ├── InstallationRequestController.php
│   │   │   │   │   └── RepairTicketController.php
│   │   │   │   └── Customer/
│   │   │   │       ├── BillingController.php
│   │   │   │       ├── PaymentController.php
│   │   │   │       └── ComplaintController.php
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php
│   │   │   └── CheckISPActive.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── SuperAdmin.php
│   │   ├── ISPAdmin.php
│   │   ├── ISP.php
│   │   ├── Technician.php
│   │   ├── Customer.php
│   │   ├── Package.php
│   │   ├── Subscription.php
│   │   ├── Invoice.php
│   │   ├── Payment.php
│   │   ├── PaymentGateway.php
│   │   ├── InstallationRequest.php
│   │   ├── RepairTicket.php
│   │   ├── Complaint.php
│   │   └── MikrotikRouter.php
│   ├── Services/
│   │   ├── PaymentGatewayService.php
│   │   ├── MikrotikService.php
│   │   ├── InvoiceService.php
│   │   └── EmailService.php
│   └── Jobs/
│       ├── GenerateMonthlyInvoice.php
│       └── SendPaymentReminder.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
└── config/
    ├── mikrotik.php
    └── payment.php
```

### Frontend (Vue 3)

```
src/
├── router/
│   └── index.js (with role-based routing)
├── store/
│   ├── index.js
│   └── modules/
│       ├── auth.js
│       ├── superadmin.js
│       ├── ispadmin.js
│       ├── technician.js
│       └── customer.js
├── views/
│   ├── Auth/
│   │   ├── Login.vue
│   │   ├── Register.vue
│   │   └── EmailVerification.vue
│   ├── SuperAdmin/
│   │   ├── Dashboard.vue
│   │   ├── ISPManagement.vue
│   │   ├── PackageManagement.vue
│   │   ├── PaymentGatewayConfig.vue
│   │   └── WebCustomization.vue
│   ├── ISPAdmin/
│   │   ├── Dashboard.vue
│   │   ├── CustomerManagement.vue
│   │   ├── PackageAssignment.vue
│   │   ├── MikrotikManagement.vue
│   │   ├── TechnicianMapping.vue
│   │   ├── PaymentApproval.vue
│   │   └── Reports.vue
│   ├── Technician/
│   │   ├── Dashboard.vue
│   │   ├── InstallationRequests.vue
│   │   └── RepairTickets.vue
│   └── Customer/
│       ├── Dashboard.vue
│       ├── Billing.vue
│       ├── Payment.vue
│       ├── InvoiceHistory.vue
│       └── Complaints.vue
├── components/
│   ├── SuperAdmin/
│   ├── ISPAdmin/
│   ├── Technician/
│   └── Customer/
└── services/
    ├── api.js
    ├── auth.js
    └── payment.js
```

## 🗄️ Database Schema

### Users & Roles

- `users` - Base user table
- `super_admins` - Super admin details
- `isps` - ISP companies
- `isp_admins` - ISP admin users
- `technicians` - Technician users
- `customers` - Customer users

### Packages & Subscriptions

- `packages` - Internet packages (created by SuperAdmin)
- `isp_subscriptions` - ISP subscription to SuperAdmin packages
- `customer_packages` - Customer internet packages (created by ISP Admin)
- `customer_subscriptions` - Customer subscriptions

### Billing & Payments

- `invoices` - All invoices
- `payments` - Payment records
- `payment_gateways` - Payment gateway configurations
- `payment_methods` - Payment methods (gateway, cash, transfer)

### Operations

- `installation_requests` - Installation tickets
- `repair_tickets` - Repair tickets
- `complaints` - Customer complaints
- `mikrotik_routers` - Mikrotik router configurations

### Customization

- `web_customizations` - Website customization settings

## 🔐 Security Features

1. **Multi-tier Authentication**
   - Separate login flows for each role
   - Laravel Sanctum for API tokens
   - Email verification required
   - 2FA support (optional)

2. **Authorization**
   - Role-based access control (RBAC)
   - Permission system
   - ISP isolation (ISP Admin can only see their data)

3. **Data Security**
   - Encrypted sensitive data
   - SQL injection prevention
   - XSS protection
   - CSRF protection

4. **Audit Trail**
   - Activity logging
   - Payment history
   - User action logs

## 🚀 Features by Role

### Super Admin

✅ ISP Registration & Verification
✅ Package Management (for ISPs)
✅ Payment Gateway Configuration
✅ Web Marketing Customization
✅ ISP Admin CRUD
✅ Analytics & Reports
✅ System Settings

### ISP Admin

✅ Customer CRUD
✅ Customer Package Management
✅ Mikrotik Integration
✅ Technician Management & Mapping
✅ Invoice Generation
✅ Payment Approval (Cash/Transfer)
✅ Installation Scheduling
✅ Repair Ticket Management
✅ Reports & Analytics

### Technician

✅ View Installation Requests
✅ View Repair Tickets
✅ Update Status
✅ Add Notes/Photos
✅ Route Optimization (future)

### Customer

✅ View Billing
✅ Pay Bills (Gateway/Cash/Transfer)
✅ Invoice History
✅ Submit Complaints
✅ Service Status
✅ Package Information

## 📱 Mobile App Preparation

- RESTful API structure
- JWT/Sanctum tokens
- Responsive JSON responses
- API documentation (Swagger)
- Rate limiting
- Push notification support

## 🔧 Third-party Integrations

### Payment Gateway

- Midtrans (Primary)
- Xendit (Alternative)
- Manual Transfer verification
- Cash payment recording

### Mikrotik

- RouterOS API integration
- User management
- Bandwidth control
- PPPoE/Hotspot support
- Real-time monitoring

## 📊 Automated Jobs

1. **Monthly Invoice Generation**
2. **Payment Reminders**
3. **Service Suspension** (non-payment)
4. **Report Generation**
5. **Email Notifications**

## 🎨 UI/UX Features

- Modern Argon Dashboard theme
- Responsive design
- Dark mode support
- Real-time notifications
- Interactive charts
- Mobile-friendly
- Progressive Web App (PWA) ready

## 📝 Development Phases

### Phase 1: Foundation (Week 1-2)

- Database setup
- Authentication system
- Base CRUD operations

### Phase 2: Core Features (Week 3-4)

- Role-specific dashboards
- Package management
- Customer management

### Phase 3: Integrations (Week 5-6)

- Payment gateway
- Mikrotik integration
- Email system

### Phase 4: Advanced Features (Week 7-8)

- Reporting
- Analytics
- Automated jobs

### Phase 5: Testing & Deployment (Week 9-10)

- Testing
- Bug fixes
- Documentation
- Deployment

## 🌐 Hosting Recommendations

### VPS (Recommended)

1. **Niagahoster VPS Bisnis** (Rp 150k/bulan)
   - 2 CPU, 4GB RAM
   - Lokasi Indonesia (fast)
   - Support 24/7 Indonesia
2. **DigitalOcean** ($12/bulan)
   - 2 CPU, 4GB RAM
   - Global locations
   - Excellent documentation

3. **Vultr** ($12/bulan)
   - Similar to DigitalOcean
   - Good performance

### Requirements

- PHP 8.2+
- MySQL 8.0+ / PostgreSQL 14+
- Node.js 18+
- Redis (for queues)
- SSL Certificate (Let's Encrypt)

### Deployment Stack

- Nginx / Apache
- PHP-FPM
- Supervisor (for queues)
- Git for deployment
- CI/CD (GitHub Actions)

## 📚 Documentation

- API Documentation (Swagger/OpenAPI)
- User Manual (per role)
- Installation Guide
- Deployment Guide
- Development Guide

## 🔄 Scalability

- Load balancing support
- Database replication
- Cache layer (Redis)
- CDN for assets
- Queue workers for background jobs
