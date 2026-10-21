# Inventory Billing ERP — User & Admin Documentation

> **Version:** 1.0  
> **Application:** Inventory Billing ERP  
> **Framework:** Laravel (PHP)  
> **Last Updated:** 2025  

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Getting Started](#2-getting-started)
   - 2.1 [How to Log In](#21-how-to-log-in)
   - 2.2 [Face Recognition Login](#22-face-recognition-login)
   - 2.3 [First-Time Setup Steps](#23-first-time-setup-steps)
   - 2.4 [Main Dashboard Overview](#24-main-dashboard-overview)
   - 2.5 [Navigation Overview (Sidebar)](#25-navigation-overview-sidebar)
3. [End-User Guide (Staff)](#3-end-user-guide-staff)
   - 3.1 [How to Create a New Sale / Bill](#31-how-to-create-a-new-sale--bill)
   - 3.2 [Adding Serial Numbers / IMEI per Order Item](#32-adding-serial-numbers--imei-per-order-item)
   - 3.3 [Processing Payment and Generating Invoice](#33-processing-payment-and-generating-invoice)
   - 3.4 [Products Delivery Page — Tracking Deliveries](#34-products-delivery-page--tracking-deliveries)
   - 3.5 [Check In / Check Out (Attendance)](#35-check-in--check-out-attendance)
   - 3.6 [My Workspace (Leaves, Payroll, Attendance)](#36-my-workspace-leaves-payroll-attendance)
   - 3.7 [Daily Workflows for Staff](#37-daily-workflows-for-staff)
4. [Admin / Owner Guide](#4-admin--owner-guide)
   - 4.1 [Managing Staff (HR Module)](#41-managing-staff-hr-module)
   - 4.2 [Attendance Management](#42-attendance-management)
   - 4.3 [Leave Management](#43-leave-management)
   - 4.4 [Payroll & Salary](#44-payroll--salary)
   - 4.5 [Advance Pay](#45-advance-pay)
   - 4.6 [EMI Settings — Configuring and Tracking EMI Sales](#46-emi-settings--configuring-and-tracking-emi-sales)
   - 4.7 [Balance Sheet Report](#47-balance-sheet-report)
   - 4.8 [CRM — Managing Customers, Leads, Follow-ups, Meetings, Tickets](#48-crm--managing-customers-leads-follow-ups-meetings-tickets)
   - 4.9 [System Settings](#49-system-settings)
   - 4.10 [User Roles and Permissions](#410-user-roles-and-permissions)
   - 4.11 [Plans & Subscriptions](#411-plans--subscriptions)
   - 4.12 [Branch Management](#412-branch-management)
5. [Common Workflows (Step-by-Step)](#5-common-workflows-step-by-step)
   - 5.1 [End-to-End Sale: Create Order → Add Serials → Invoice → Delivery](#51-end-to-end-sale-create-order--add-serials--invoice--delivery)
   - 5.2 [Staff Attendance: Check-in → Attendance Recorded → Payroll Calculation](#52-staff-attendance-check-in--attendance-recorded--payroll-calculation)
   - 5.3 [EMI Sale Workflow](#53-emi-sale-workflow)
6. [Troubleshooting / FAQs](#6-troubleshooting--faqs)

---

## 1. Project Overview

**Inventory Billing ERP** is a comprehensive, multi-branch Enterprise Resource Planning (ERP) system built on the Laravel PHP framework. It is designed for retail, wholesale, and service businesses that need an integrated platform combining inventory management, billing, human resources, and customer relationship management under one roof.

### Core Modules at a Glance

| Module | Description |
|---|---|
| **ERP** | Sales, Purchases, Products, Inventory, Delivery, Returns, Vendors |
| **CRM** | Customers, Leads, Follow-ups, Meetings, Support Tickets |
| **Accounting** | Balance Sheet, Income Statement, P&L, Expenses, GST Reports, Cash/Bank Book |
| **HR** | Staff Management, Attendance, Leaves, Payroll, Advance Pay, Departments, Designations, Holidays |
| **Settings** | Shop Configuration, User Management, Plans, SMTP, E-Way Bill, E-Invoice |

### Who Is This System For?

- **Business Owners / Admins** who need complete visibility into sales, inventory, finances, and HR across one or more branches.
- **Staff / Sales Executives** who process day-to-day sales, manage deliveries, and track their attendance.
- **HR Managers** who manage employee records, attendance devices, leave approvals, and payroll.
- **Inventory Managers** who track product stock, manage purchase orders, and oversee delivery fulfilment.
- **Customers** who may access their account, invoices, and support tickets via a dedicated portal.

### Technology Stack

- **Backend:** Laravel (PHP 8+)
- **Database:** MySQL
- **Frontend:** Blade Templates, Bootstrap, JavaScript
- **PDF Generation:** Built-in PDF engine for A4 and A5 invoice formats
- **Biometric Integration:** ZKTeco device sync for attendance
- **Face Recognition:** Browser-based face descriptor matching via camera API
- **GST Compliance:** Full Indian GST reporting (GSTR-1, 2, 3B, 9, 9C)
- **Currency:** Indian Rupee (₹) formatting throughout

---

## 2. Getting Started

### 2.1 How to Log In

The login page is the first screen you see when you visit the application. The URL is the root of the application (e.g., `http://yourdomain.com/` or `http://localhost/inventory-billing/`).

**Steps to log in:**

1. Open your web browser and navigate to the application URL.
2. You will see the login form with two fields: **Username** and **Password**.
3. Enter your username (provided by your administrator).
4. Enter your password.
5. Click the **Login** button.
6. If your credentials are correct, you will be redirected to the main Dashboard.
7. If you see an error message such as "Invalid credentials", double-check your username and password. Usernames and passwords are case-sensitive.

> **Note:** If you have forgotten your password, contact your system administrator to reset it. There is no self-service password reset unless SMTP email is configured and the feature is enabled.

### 2.2 Face Recognition Login

The system supports an optional face recognition login method that uses your device camera to identify you without typing a password. This feature must be set up by an administrator first (your face descriptor must be registered in the system).

**Using Face Recognition Login:**

1. On the login page, click the button labelled **"Login with Face"**.
2. Your browser will request permission to access your camera. Click **Allow**.
3. A live camera feed will appear in the login area.
4. Position your face clearly in front of the camera, ensuring good lighting.
5. The system will automatically scan your face and compare it against all stored face descriptors in the database.
6. If a match is found, you will be logged in immediately without needing a password.
7. If no match is found after a few seconds, the system will display a message. You can try again or fall back to standard username/password login.

**Requirements for Face Recognition:**

- A working webcam or front-facing camera on your device.
- A modern browser (Chrome or Firefox recommended).
- Your face descriptor must have been previously registered by an administrator.
- Adequate lighting — avoid sitting with a bright light source directly behind you.

**Registering Your Face (Admin Task):**

Administrators can register a staff member's face descriptor through the HR / Staff profile page. The system captures the face using the camera and stores the descriptor in the database linked to that user account.

### 2.3 First-Time Setup Steps

When the system is deployed for the first time, the administrator should complete the following setup checklist before regular use begins.

1. **Complete Shop Settings:** Navigate to **Settings → Shop Settings** and fill in all four steps:
   - Step 1: Basic business information (name, address, phone, GSTIN, etc.)
   - Step 2: Bank details (account number, IFSC, bank name)
   - Step 3: System preferences (currency, date format, invoice prefix, etc.)
   - Step 4: Upload your shop logo and any branding media.

2. **Create User Accounts:** Go to **Settings → Users** and create accounts for all staff members. Assign each user an appropriate role (admin, sub-admin, staff, hr, inventory-manager, or customer).

3. **Set Up Departments and Designations:** Under **HR → Departments** and **HR → Designations**, define your organisational structure before creating employee records.

4. **Add Products and Categories:** Under **Products**, add your product catalogue with names, HSN codes, GST rates, units, and opening stock levels.

5. **Add Vendors and Customers:** Under **Vendors** add your suppliers. Under **CRM → Customers** add your key customers with their GST/PAN details.

6. **Configure SMTP Email:** Go to **Settings → SMTP Settings** and enter your email server details so that the system can send invoices, leave approval notifications, and ticket updates by email.

7. **Configure Geofence (for Attendance):** Go to **Settings → Company Rules** and enter the office GPS coordinates (latitude and longitude) and the geofence radius in metres. This controls where staff can check in from.

8. **Set Up Plans (SaaS Mode):** If running the system in multi-tenant SaaS mode, configure subscription plans at **/plans** before onboarding branch clients.

9. **Test a Sale:** Create a test sale to verify that invoices generate correctly, taxes are applied properly, and PDFs can be downloaded.

10. **Test Attendance Check-In:** Have a staff member perform a test check-in to confirm GPS capture and geofence enforcement are working.

### 2.4 Main Dashboard Overview

After logging in, you land on the main Dashboard. This page provides a real-time snapshot of your business across all key areas. The widgets displayed depend on your role and the Dashboard Settings configured by the administrator.

**Key Dashboard Widgets:**

- **Total Sales Amount** — The aggregate value of all sales invoices within the selected period.
- **Total Purchase Amount** — The aggregate value of all purchase orders within the selected period.
- **Total Expense Amount** — The sum of all recorded expenses.
- **Invoice Counts** — Number of invoices created (sales, purchase, quotation).
- **Recent Sales Table** — A quick-view table of the latest sales transactions, showing customer name, invoice number, amount, and status.
- **Recent Purchases Table** — A quick-view table of the latest purchase orders.
- **Products Delivery Widget** — Shows pending delivery items with ordered quantity vs. delivered quantity.
- **CRM Pipeline Metrics** — Shows number of active leads, open follow-ups scheduled for today, and upcoming meetings.
- **HR Snapshot** — Displays today's attendance count (present/absent), pending leave requests, and next payroll run date.

**Period Selector:** Most widgets have a period filter (Today, This Week, This Month, Custom Range) at the top of the dashboard or on individual widgets.

**Today Alerts Modal:** A notification modal that appears (or can be triggered by clicking the bell icon in the header) showing time-sensitive items: meetings scheduled for today, follow-ups due today, pending deliveries, and EMI instalments that fall due this month.

### 2.5 Navigation Overview (Sidebar)

The left sidebar is the primary navigation menu. It is role-sensitive — you will only see menu items relevant to your role.

| Menu Section | Sub-items |
|---|---|
| **Dashboard** | Main overview |
| **Sales** | Add Sale, Sale List, Quotations, Advance Receipts, Delivery |
| **Purchase** | Add Purchase, Purchase List, Purchase Returns |
| **Products** | Product List, Categories, Units, Stock Adjustment |
| **Vendors** | Vendor List, Vendor Ledger |
| **Inventory** | Stock Report, Low Stock Alerts |
| **CRM** | Customers, Leads, Follow-ups, Meetings, Tickets |
| **Accounting** | Balance Sheet, Income Statement, P&L, Expenses, Cash Book, Bank Book, GST Reports |
| **HR** | Staff, Attendance, Leaves, Payroll, Advance Pay, Departments, Designations, Holidays |
| **Settings** | Shop Settings, Company Rules, SMTP, Plans, Users, Delivery Terms, Dashboard Settings |

The header bar at the top contains: the application logo, a search bar, the **Today Alerts** bell icon, and the logged-in user's profile menu (profile, change password, logout).

---

## 3. End-User Guide (Staff)

This section is intended for front-line staff — sales executives, billing operators, and delivery staff — who use the system for day-to-day operations. Instructions are written to be simple and sequential.

### 3.1 How to Create a New Sale / Bill

Creating a new sale is done through the POS (Point of Sale) interface at the `/add-sales` route.

1. Click **Sales** in the left sidebar, then click **Add Sale**.
2. The POS screen will open. At the top, you will see fields for **Customer**, **Invoice Date**, **Invoice Number** (auto-generated but editable), and **Sale Type** (Sale / Quotation / Advance Receipt).
3. **Select the Customer:** Start typing the customer's name in the Customer field. A dropdown will suggest matching customers. If the customer is new, you can click **Add New Customer** to create a record on the fly.
4. **Add Products:** In the product search box within the order items section, type the product name or SKU. Select the product from the dropdown.
5. Once a product is added to the order, a row appears with fields for **Quantity**, **Unit Price**, **Discount**, **GST Rate**, and **Line Total**. Adjust the quantity and price as required.
6. To add more products to the same invoice, repeat step 4–5 for each product.
7. **Apply Discount (optional):** You can apply a discount per item in the discount column, or apply a global discount at the bottom of the order summary.
8. **Check Totals:** The right-side summary panel shows the subtotal, total discount, GST breakdown (CGST + SGST or IGST depending on supply type), and the grand total.
9. **Select Sale Type:** Choose between:
   - **Sale** — a regular billing invoice.
   - **Quotation** — a proforma or estimate that can later be converted to a sale.
   - **Advance Receipt** — to record an advance payment against a future order.
10. Proceed to payment (see Section 3.3).

### 3.2 Adding Serial Numbers / IMEI per Order Item

For electronics, mobile phones, and other serialised products, the system supports capturing IMEI or serial numbers against each unit sold.

1. After adding a serialised product to the order in the POS, look for the **Serial / IMEI** icon or button next to that line item. It is typically a barcode or tag icon.
2. Click it. A modal dialog will open asking you to enter the serial/IMEI numbers.
3. Enter one serial number per line, or scan them using a barcode scanner (scanners that act as keyboard input are supported).
4. The number of serial entries must match the quantity on the order line. For example, if you are selling 3 units, you must enter 3 distinct serial numbers.
5. Click **Save** or **Confirm** in the modal to attach the serials to that line item.
6. A small indicator next to the line item will confirm that serials have been recorded.
7. These serial numbers will appear on the printed invoice and are stored in the database for warranty and return tracking.

> **Tip:** If your barcode scanner has a scan-to-enter feature, you can scan each unit's barcode directly into the IMEI modal field for fast entry.

### 3.3 Processing Payment and Generating Invoice

After all products are added to the order, you need to record payment details before finalising the invoice.

**Standard Payment (Cash or Online/UPI):**

1. In the payment section of the POS, select the **Payment Method**: **Cash** or **Online/UPI**.
2. Enter the **Amount Received** (for cash payments, the system can calculate change).
3. If paying by UPI, optionally record the UPI reference/transaction ID.
4. Click **Save & Generate Invoice** (or **Save Sale**).
5. The invoice will be saved and a PDF generation prompt will appear.

**Generating Invoice PDF:**

1. After saving the sale, click the **Download Invoice** or **Print Invoice** button.
2. You will be prompted to select the invoice size: **A4** or **A5**.
3. Select the appropriate size and click **Generate**.
4. The PDF will open in a new browser tab. You can print it directly or download it to your computer.
5. The invoice PDF includes: shop logo, business details, GSTIN, customer details, itemised product table with HSN codes and GST rates, payment summary, terms and conditions, and the digital signature area.

**EMI Payment:** See Section 4.6 for detailed EMI payment processing.

**Partial Payment:**

1. If the customer is paying a partial amount, enter only the amount being paid now.
2. The balance will be shown as "Due" on the invoice and tracked as a receivable in the system.

### 3.4 Products Delivery Page — Tracking Deliveries

After a sale is created, the products may need to be physically delivered. The Delivery page tracks the fulfilment status of each order item.

1. Click **Sales → Delivery** in the sidebar. The Products Delivery page will open.
2. You will see a list of sales orders that have pending delivery items.
3. Each order row can be expanded to show the individual products within it, with columns for:
   - **Product Name**
   - **Ordered Quantity** — the total qty on the invoice.
   - **Delivered Quantity** — how many units have been delivered so far.
   - **Pending Quantity** — the balance remaining to deliver.
4. **Recording a Delivery:**
   - Click the **Deliver** button for a specific order.
   - A form will appear. Enter the quantity being delivered in this batch.
   - The system supports **partial delivery**: you can deliver fewer units than ordered, and the pending balance will be updated. The order will stay open until all items are fully delivered.
   - Enter the delivery date.
   - Optionally add delivery notes or the name of the person receiving the goods.
5. Click **Save Delivery** to record the delivery.
6. The delivered quantity column will update immediately.

**Generating a Delivery Challan PDF:**

1. On the Delivery page, find the order and click **Generate Challan**.
2. A delivery challan PDF will be generated showing: order reference, customer details, delivery date, itemised list of products being delivered with quantities.
3. Print and send this challan with the physical goods as proof of delivery.

### 3.5 Check In / Check Out (Attendance)

Staff attendance is recorded digitally through the Check In / Check Out buttons in the application header. The system captures your GPS location and enforces a geofence radius set by your administrator.

**Checking In:**

1. When you arrive at work and log in to the system, look at the top header bar.
2. You will see a green **"Check In"** button.
3. Click **Check In**.
4. Your browser will request permission to access your location (GPS). Click **Allow**.
5. The system will compare your current GPS coordinates to the office location configured in Settings.
6. If you are within the allowed geofence radius, your check-in will be recorded with the current timestamp and your GPS coordinates.
7. If you are outside the geofence, the system will display a warning message and may block the check-in (depending on administrator configuration).
8. After a successful check-in, the button in the header will change to a red **"Check Out"** button.

**Checking Out:**

1. When you are leaving for the day, click the red **"Check Out"** button in the header.
2. The system will again capture your GPS location.
3. Your check-out time is recorded. Your working hours for the day are automatically calculated (Check Out time minus Check In time).
4. The button returns to the "Check In" state for the next working day.

> **Important:** You must allow location access in your browser for attendance to work. If you deny location access, the check-in/out will fail. Contact your IT support if you are unable to allow location access on your browser or device.

### 3.6 My Workspace (Leaves, Payroll, Attendance)

Staff members can view their own HR-related information through the profile or workspace section.

**Viewing Your Attendance:**

1. Navigate to your profile or the HR section visible to your role.
2. Your daily attendance records show: Date, Check-In Time, Check-Out Time, Working Hours, and Status (Present / Absent / Late / Half Day).

**Applying for Leave:**

1. Go to **HR → Leaves** (or the leave section available to your role).
2. Click **Apply for Leave**.
3. Fill in the leave application form:
   - **Leave Type:** (e.g., Casual Leave, Sick Leave, Earned Leave)
   - **From Date** and **To Date**
   - **Reason:** A brief description
4. Click **Submit**. Your leave request will be sent to HR / Admin for approval.
5. You will receive a notification (and email if SMTP is configured) when your leave is approved or rejected.

**Viewing Your Salary Slip:**

1. Go to **HR → Payroll** or your profile page.
2. Select the month and year for which you want to view your salary slip.
3. The salary slip will show: Basic Salary, Allowances, Deductions, Incentives, Advance Pay deducted, Net Pay, and the payment status.
4. You can download the salary slip as a PDF.

### 3.7 Daily Workflows for Staff

A typical day for a sales staff member in the system follows this pattern:

**Morning:**
1. Log in to the application.
2. Click **Check In** in the header to record your attendance.
3. Review the Dashboard for any pending deliveries or follow-ups assigned to you.
4. Check the **Today Alerts** modal (bell icon) for meetings or follow-ups due today.

**During Work:**
5. Process customer sales using **Sales → Add Sale**.
6. Collect payment and generate invoices.
7. Update delivery status for orders ready to ship via **Sales → Delivery**.
8. Respond to customer support tickets if assigned.

**End of Day:**
9. Ensure all sales are saved and invoices are generated for customers.
10. Click **Check Out** in the header to record your departure.
11. Review any pending tasks for the next day.

---
## 4. Admin / Owner Guide

This section is for administrators, business owners, HR managers, and sub-admins who manage the system configuration and oversee all modules.

### 4.1 Managing Staff (HR Module)

The HR module allows administrators to manage the full employee lifecycle from onboarding to payroll.

**Adding a New Staff Member:**

1. Go to **HR → Staff** in the sidebar.
2. Click **Add Staff** or **New Employee**.
3. Fill in the staff profile form with the following details:
   - Full name, date of birth, gender
   - Department and Designation
   - Date of joining
   - Contact information (phone, email, address)
   - Emergency contact details
   - Identity documents (Aadhaar number, PAN number)
   - Bank account details for salary payment (account number, IFSC, bank name)
   - Salary structure (basic salary, HRA, allowances, deductions)
4. Optionally register the staff member's face for Face Recognition login by clicking **Register Face** and following the camera capture steps.
5. Click **Save** to create the staff record.
6. If SMTP is configured, the system will send a welcome email with login credentials to the staff member.

**Editing Staff Records:**

1. Go to **HR → Staff** and click on the employee's name or the Edit button.
2. Update the required fields and click **Save**.

**Deactivating a Staff Member:**

1. Edit the staff profile and set their status to **Inactive** or **Terminated**.
2. Inactive staff cannot log in but their historical records are retained.

### 4.2 Attendance Management

The system supports two modes of attendance recording: web-based check-in (with GPS) and biometric device sync (ZKTeco).

**Viewing Attendance Records:**

1. Go to **HR → Attendance**.
2. Filter by employee, date range, or department.
3. The attendance table shows: Date, Employee, Check-In Time, Check-Out Time, Working Hours, and Attendance Status.

**Manual Attendance Entry:**

If a staff member forgot to check in or their device check failed, administrators can manually create or edit attendance records.

1. Go to **HR → Attendance** and click **Add Attendance** or click the edit icon on an existing record.
2. Select the employee, date, check-in time, and check-out time.
3. Save the record.

**ZKTeco Biometric Device Sync:**

1. The biometric device must be connected to the same network as the server.
2. Go to **Settings** or the Biometric settings page and enter the device's IP address and port.
3. Run the sync command or click **Sync Now** to pull punch records from the device.
4. Alternatively, the system can be configured to auto-sync on a schedule via the `SyncBiometricPunches` Artisan command in the scheduler.

**Attendance Reports:**

The attendance module generates monthly attendance reports showing total present days, absent days, late arrivals, and total working hours per employee — used as input for payroll calculation.

### 4.3 Leave Management

The leave management system handles leave applications, approvals, and leave balance tracking.

**Leave Types:**

Before employees can apply, configure the leave types in the system (e.g., Casual Leave, Sick Leave, Earned Leave, Maternity Leave). Each leave type has a configurable annual allocation.

**Approving or Rejecting a Leave Request:**

1. Go to **HR → Leaves**.
2. You will see a list of pending leave requests sorted by date.
3. Click on a leave request to view its details: employee name, leave type, dates, duration, and reason.
4. Click **Approve** to grant the leave or **Reject** to decline it.
5. Optionally add a rejection reason before clicking Reject.
6. The employee will receive a system notification and email (if SMTP is configured) informing them of the decision.

**Leave Balance Tracking:**

Each employee has a leave balance per leave type. The system deducts from the balance when leave is approved. Administrators can view and manually adjust leave balances from the employee's HR profile.

**Leave Calendar:**

A calendar view shows which employees are on leave on any given day, helping managers plan staffing.

### 4.4 Payroll & Salary

The payroll module calculates monthly salaries based on attendance data, salary structures, deductions, and incentives.

**Running Monthly Payroll:**

1. Go to **HR → Payroll**.
2. Select the **Month** and **Year** for which you want to generate payroll.
3. Click **Generate Payroll** or **Run Payroll**.
4. The system automatically calculates:
   - Basic Salary (prorated based on days present)
   - HRA and other allowances
   - Late deductions (if configured)
   - Absent day deductions
   - Any advance salary deductions (see Section 4.5)
   - Incentives (if applicable)
   - Net Payable amount
5. Review the payroll register showing each employee's calculation.
6. Make any manual adjustments needed.
7. Click **Finalise Payroll** to lock the payroll for that month.

**Generating Salary Slips:**

1. After finalising payroll, click **Generate Slips** to create PDF salary slips for all employees.
2. Individual salary slips can also be viewed and downloaded from each employee's payroll record.
3. Salary slips can be emailed directly to employees if SMTP is configured.

### 4.5 Advance Pay

The system allows employees to request advance salary, and administrators to record and manage advance payments.

**Recording an Advance Payment:**

1. Go to **HR → Advance Pay**.
2. Click **Add Advance**.
3. Select the employee, enter the advance amount, and the date.
4. Specify the repayment schedule: how many months the advance should be deducted across.
5. Save the advance record.

**Advance Deduction in Payroll:**

When payroll is run for subsequent months, the system automatically deducts the advance instalment from the employee's net pay until the full advance is recovered.

**Viewing Advance Balance:**

The advance pay register shows: total advance given, amount recovered to date, and remaining balance for each employee.

### 4.6 EMI Settings — Configuring and Tracking EMI Sales

The system supports EMI-based sales modelled after financer products like Bajaj Finserv. When a customer wants to purchase on EMI, the sale is recorded with full EMI financing details.

**Processing an EMI Sale:**

1. Create a new sale as normal (see Section 3.1).
2. In the payment section, select **EMI** as the payment method.
3. A detailed EMI form will appear. Fill in the following fields:
   - **Down Payment Amount** — the amount paid upfront by the customer.
   - **Loan Amount** — the amount being financed.
   - **Interest Rate** — annual interest rate (e.g., 0% or standard financer rate).
   - **Tenure (Months)** — the number of EMI months (e.g., 6, 12, 24).
   - **Monthly Installment** — calculated automatically or entered manually.
   - **Financer** — select or type the financer name (e.g., Bajaj Finserv).
   - **Customer Aadhaar No** — the customer's 12-digit Aadhaar number.
   - **Customer PAN No** — the customer's PAN card number.
   - **Guarantor Name** — name of the guarantor (if applicable).
4. Click **Save Sale** to record the EMI sale.

**Tracking Pending EMIs:**

The system tracks which EMI instalments are due each month. Pending EMIs appear in the **Today Alerts** modal under the bell icon notification. Admins can view all pending EMIs from the Sales module filtered by payment type.

**EMI Status:**

- Each EMI instalment has a status: Pending, Received, or Overdue.
- When the customer pays an instalment, mark it as received in the system to keep the records current.

### 4.7 Balance Sheet Report

The Balance Sheet report provides a snapshot of the business's financial position at a given date. It is accessible at `/balance-sheet`.

**Generating a Balance Sheet:**

1. Go to **Accounting → Balance Sheet**.
2. Select the **From Date** and **To Date** for the reporting period.
3. Click **Generate** or **Apply**.
4. The report will display:

**Assets Section:**
- Closing Stock (current value of inventory)
- Cash in Hand (total cash balance from Cash Book)
- Bank Account Balances (individual bank account balances)
- Accounts Receivable (outstanding customer balances)

**Liabilities Section:**
- Accounts Payable (outstanding vendor balances)
- GST Payable (GST collected but not yet remitted)
- Profit & Loss (net profit or loss for the period)

**Exporting the Balance Sheet:**

1. Click **Export PDF** to download a formatted PDF version.
2. Click **Export Excel** to download a spreadsheet version for further analysis.

All amounts are formatted in Indian Rupee (INR) notation with lakh and crore separators.

### 4.8 CRM — Managing Customers, Leads, Follow-ups, Meetings, Tickets

**Customer Management:**

1. Go to **CRM → Customers**.
2. Each customer record contains: name, company, contact details, GSTIN, PAN, credit limit, and transaction history.
3. Click **Add Customer** to create a new customer or **Edit** to update an existing one.

**Lead Pipeline:**

1. Go to **CRM → Leads**.
2. Leads represent potential customers or sales opportunities.
3. Each lead has a status (New, Contacted, Qualified, Proposal, Negotiation, Won, Lost).
4. Move leads through the pipeline by updating their status.

**Follow-ups:**

1. Go to **CRM → Follow-ups**.
2. Schedule a follow-up against a customer or lead by specifying: follow-up date, type (Call, Email, Visit), and notes.
3. Follow-ups due today appear in the Today Alerts modal.

**Meetings:**

1. Go to **CRM → Meetings**.
2. Schedule a meeting with participants, meeting date/time, location, and agenda.
3. Meetings due today appear in the Today Alerts notification modal.
4. Reminders can be sent by email if SMTP is configured.

**Support Tickets:**

1. Go to **CRM → Tickets**.
2. Customers can raise support tickets (or staff can raise them on behalf of customers).
3. Each ticket has: subject, description, priority, category, assigned staff, and status (Open, In Progress, Resolved, Closed).
4. Email notifications are sent on ticket creation and status updates (if SMTP is configured).
5. Admins can filter tickets by status, priority, assigned staff, or date.

### 4.9 System Settings

#### 4.9.1 Shop Settings

Shop Settings is a 4-step wizard accessible at **Settings → Shop Settings**.

**Step 1 — Basic Information:**
- Shop/Business name
- Address (line 1, line 2, city, state, PIN code)
- Phone number(s)
- Email address
- GSTIN (15-character GST registration number)
- Business type

**Step 2 — Bank Details:**
- Bank account number
- Bank name
- IFSC code
- Account holder name
- Branch name

**Step 3 — System Preferences:**
- Invoice number prefix (e.g., INV-, BILL-)
- Financial year start month
- Date format preference
- Default GST rate
- Default terms of payment (days)
- Invoice footer note

**Step 4 — Media & Branding:**
- Upload shop logo (displayed on invoices, delivery challans, and login page)
- Upload digital signature image (displayed on invoice PDF)
- Optionally upload a stamp image

#### 4.9.2 Company Rules

Company Rules contains operational settings that govern system behaviour.

- **Attendance Geofence:** Set the office latitude, longitude, and geofence radius (in metres). Staff must be within this radius to check in or check out.
- **Working Hours:** Define the standard start and end time for the working day.
- **Late Arrival Threshold:** Number of minutes after which a check-in is flagged as late.
- **Leave Policies:** Configure leave carry-forward rules and maximum leave days per type.

#### 4.9.3 Delivery Terms & Conditions

Configure the text that appears in the Delivery Terms section of delivery challans and invoices.

1. Go to **Settings → Delivery Terms**.
2. Two text areas are provided: one for **English** and one for **Gujarati**.
3. Enter the terms and conditions in both languages as required.
4. Click **Save**. These terms will automatically appear on all generated delivery challan PDFs.

#### 4.9.4 Dashboard Settings

Control which widgets appear on the main dashboard and for which roles.

1. Go to **Settings → Dashboard Settings**.
2. You will see a list of available widgets (Sales Summary, Purchase Summary, HR Snapshot, CRM Pipeline, Delivery Widget, etc.).
3. Toggle each widget on or off.
4. Some widgets may have role-based visibility settings — configure which roles can see which widgets.
5. Save the configuration. Changes take effect immediately for all users.

#### 4.9.5 SMTP Email Settings

Configure the outgoing email server so the system can send automated emails.

1. Go to **Settings → SMTP Settings**.
2. Fill in the following fields:
   - **SMTP Host** — e.g., `smtp.gmail.com` or your email server hostname.
   - **SMTP Port** — typically 465 (SSL) or 587 (TLS/STARTTLS).
   - **Encryption** — select SSL or TLS.
   - **Username** — the email address used to send emails.
   - **Password** — the email password or app-specific password. Use the eye icon to toggle visibility.
   - **Status** — Enable or Disable SMTP sending.
3. Click **Save & Test** to verify the configuration. A test email will be sent to the configured address.
4. If the test fails, verify the host, port, credentials, and encryption settings.

### 4.10 User Roles and Permissions

The system uses a role-based access control (RBAC) system. The available roles are:

| Role | Access Level |
|---|---|
| **admin** | Full access to all modules, settings, and data across all branches |
| **sub-admin** | Broad access but cannot manage system settings or plans |
| **staff** | Access to Sales, Deliveries, and their own HR data |
| **hr** | Full access to HR module; can view but not modify financial records |
| **inventory-manager** | Access to Products, Purchase, Inventory; limited Sales access |
| **customer** | Access only to their own account, invoices, and support tickets |

**Creating a User and Assigning a Role:**

1. Go to **Settings → Users**.
2. Click **Add User**.
3. Enter the name, username, email, and password.
4. Select the role from the dropdown.
5. Assign the user to a branch (if multi-branch is enabled).
6. Click **Save**. The user can now log in with the assigned credentials.

**The AutoPermission Middleware** automatically applies permissions based on roles. Individual permission overrides can also be configured per user if fine-grained control is needed.

### 4.11 Plans & Subscriptions

In SaaS mode, the system supports subscription plans that control feature access and resource limits per tenant/branch.

**Managing Plans:**

1. Navigate to `/plans` or **Settings → Plans**.
2. The Plans list shows all configured subscription tiers.
3. Click **Add Plan** to create a new plan.

**Plan Configuration Fields:**

- **Plan Name** — e.g., Starter, Professional, Unlimited.
- **Price** — Monthly or annual price in INR.
- **Duration** — Subscription duration in days or months.
- **User Limit** — Maximum number of user accounts allowed under this plan.
- **Branch Limit** — Maximum number of branches:
  - Starter: 1 branch
  - Professional: 3 branches
  - Unlimited: no limit (null)
- **Storage Limit** — Maximum file storage allowed (in MB or GB).
- **Status** — Active or Inactive.

**Editing or Deactivating a Plan:**

1. Click **Edit** on an existing plan to modify its details.
2. To retire a plan, set its status to **Inactive**. Existing subscribers will retain access until their subscription expires.

### 4.12 Branch Management

The system supports multiple branches for businesses with more than one location.

**Adding a Branch:**

1. Go to **Settings → Branches** (or the branch management section).
2. Click **Add Branch**.
3. Enter the branch name, address, contact number, and GSTIN (if the branch has a separate GST registration).
4. Assign a sub-admin or manager to the branch.
5. Save.

**Branch-Level Data Isolation:**

Each branch maintains its own set of sales, purchases, inventory, and HR data. Admins with the appropriate role can view consolidated reports across all branches. Branch-level users only see data for their assigned branch.

---

## 5. Common Workflows (Step-by-Step)

### 5.1 End-to-End Sale: Create Order → Add Serials → Invoice → Delivery

This workflow covers the complete journey of a serialised product sale from order creation to customer delivery.

**Step 1: Open the POS**
1. Log in as staff or admin.
2. Navigate to **Sales → Add Sale**.

**Step 2: Select Customer and Add Products**
3. Search for and select the customer.
4. Add the product(s) to the order by searching by name or SKU.
5. Set the quantity, price, and verify the GST calculation.

**Step 3: Add IMEI/Serial Numbers**
6. For each serialised product, click the Serial/IMEI button on the line item.
7. Enter or scan the serial numbers — one per unit sold.
8. Confirm to close the IMEI modal.

**Step 4: Process Payment**
9. Select the payment method (Cash, UPI, or EMI).
10. Enter the amount received.
11. Click **Save Sale** to finalise the order.

**Step 5: Generate Invoice**
12. After saving, click **Print Invoice** or **Download Invoice**.
13. Select A4 or A5 and generate the PDF.
14. Print or share the invoice with the customer.

**Step 6: Record Delivery**
15. Navigate to **Sales → Delivery**.
16. Locate the order just created.
17. Click **Deliver**, enter the delivery quantity and date.
18. Save the delivery record.
19. Optionally generate and print the Delivery Challan to accompany the goods.

**Step 7: Verify Completion**
20. The order status updates to fully delivered once all items are delivered.
21. The transaction appears in the sales report and accounting ledger.

### 5.2 Staff Attendance: Check-in → Attendance Recorded → Payroll Calculation

This workflow covers the full attendance-to-payroll cycle.

**Step 1: Staff Checks In**
1. Staff member logs in to the system at the start of the workday.
2. Clicks the green **Check In** button in the header.
3. Allows browser location access.
4. System verifies GPS coordinates against the office geofence.
5. Check-in is recorded with timestamp and GPS data.

**Step 2: Staff Checks Out**
6. At end of day, staff clicks the red **Check Out** button.
7. Location is captured again.
8. System calculates working hours = Check-out time minus Check-in time.
9. Attendance record for the day is complete.

**Step 3: Attendance Review (Admin)**
10. Admin navigates to **HR → Attendance** and reviews the monthly attendance register.
11. Any anomalies (missing check-outs, incorrect times) can be corrected manually.
12. Leave days are automatically excluded from payroll deductions if leave was approved.

**Step 4: Payroll Run**
13. Admin navigates to **HR → Payroll** and selects the month.
14. Clicks **Generate Payroll**.
15. System calculates:
    - Days present from attendance data
    - Prorated basic salary
    - Deductions for absences, lates
    - Advance salary deductions (if any)
    - Incentives (if configured)
16. Admin reviews and adjusts if needed.
17. Clicks **Finalise Payroll**.

**Step 5: Salary Slips**
18. After finalisation, generate salary slips for all employees.
19. Staff can view and download their salary slips from their profile.

### 5.3 EMI Sale Workflow

This covers processing a sale where the customer pays through an EMI finance scheme.

**Step 1: Create the Sale**
1. Open the POS at **Sales → Add Sale**.
2. Select the customer and add products to the order.

**Step 2: Select EMI Payment**
3. In the payment section, choose **EMI** as the payment method.
4. The EMI details form appears.

**Step 3: Fill in EMI Details**
5. Enter the **Down Payment** amount.
6. Enter the **Loan Amount** (product total minus down payment).
7. Set the **Tenure** in months (e.g., 12 months).
8. Enter the **Interest Rate** (0% for zero-cost EMI or the applicable rate).
9. The **Monthly Installment** amount is displayed automatically.
10. Select the **Financer** (e.g., Bajaj Finserv).
11. Enter the customer's **Aadhaar Number** and **PAN Number**.
12. Enter the **Guarantor Name** if required.

**Step 4: Save the Sale**
13. Click **Save Sale** to record the EMI transaction.
14. The invoice PDF will reflect the EMI payment structure.

**Step 5: Track EMI Collections**
15. Each month, open the **Today Alerts** modal to see which EMI instalments are due.
16. When the customer pays an instalment, locate the EMI record and mark it as **Received**.
17. Overdue instalments are highlighted for follow-up.

---

## 6. Troubleshooting / FAQs

**Q: I cannot log in. What should I do?**
A: Check that your username and password are correct and case-sensitive. If you have forgotten your password, contact your administrator. Administrators can reset passwords from the User management section in Settings.

**Q: The face recognition login is not working.**
A: Ensure your browser has permission to access the camera. Check that adequate lighting is available. If the face descriptor was not properly registered, ask your administrator to re-register your face from the HR profile page. Chrome and Firefox are the most reliable browsers for this feature.

**Q: My Check In / Check Out is failing with a location error.**
A: The system requires GPS access. Open your browser settings and ensure location access is allowed for the application's domain. On mobile devices, check that location services are enabled at the device level as well.

**Q: My check-in was rejected because I am "outside the geofence".**
A: This means your current GPS location is outside the office radius configured by your administrator. If you are physically at the office and still getting this error, the GPS reading may be inaccurate (this can happen indoors or on older devices). Contact your administrator to review or temporarily increase the geofence radius, or to manually record your attendance.

**Q: The invoice PDF is not generating.**
A: Ensure your browser allows popups and new tabs from the application domain. Clear your browser cache and try again. If the PDF still fails, check with your administrator that the PDF generation library is correctly installed on the server.

**Q: GST is not calculating correctly on the invoice.**
A: Verify the product's GST rate is correctly set in the product catalogue. Also confirm whether the supply is intra-state (CGST + SGST applies) or inter-state (IGST applies), based on the customer's registered state vs. the shop's state. This is configured in Shop Settings and the customer's GST registration details.

**Q: Emails (invoices, leave notifications, tickets) are not being received.**
A: Go to **Settings → SMTP Settings** and verify that the SMTP configuration is correct and status is set to Enabled. Run the test email to confirm delivery. Check spam/junk folders on the recipient's email. Common causes: incorrect SMTP password, wrong port/encryption combination, or the email server requiring an App Password (common with Gmail).

**Q: How do I add a new branch?**
A: Navigate to **Settings → Branches** and click **Add Branch**. Fill in the branch details. Note that the number of branches you can add depends on your current subscription plan. Upgrade to a higher plan if you need more branches.

**Q: How do I export GST reports?**
A: Go to **Accounting → GST Reports** and select the report type (GSTR-1, GSTR-2, GSTR-3B, GSTR-9, or GSTR-9C). Set the date range, then click **Export Excel** or **Export PDF**. For GSTR-9C reconciliation, you can also import a GST Workbook via the **GST Workbook Validation** feature which cross-checks your filed data.

**Q: The biometric device sync is not pulling attendance data.**
A: Verify that the ZKTeco device and the server are on the same network. Check the device's IP address and port in the biometric settings. Ensure the device is powered on and accessible by pinging its IP from the server. Run the `SyncBiometricPunches` Artisan command manually to check for error messages.

**Q: How do I view the balance sheet for a specific period?**
A: Go to **Accounting → Balance Sheet** at `/balance-sheet`. Use the date range selector to specify your From and To dates and click Generate. The report supports export to both PDF and Excel formats.

**Q: A staff member was added but did not receive their login credentials.**
A: This email is sent via SMTP. Verify that SMTP is configured and enabled in Settings. You can also manually tell the staff member their username and temporary password, then have them change it after first login via their Profile page.

**Q: How do I change my password?**
A: Click on your profile avatar/name in the top-right header and select **Change Password**. Enter your current password, then your new password twice, and click Save.

**Q: Can I customise the invoice terms and conditions?**
A: Yes. Go to **Settings → Delivery Terms** to set the text that appears in the terms section. You can enter text in both English and Gujarati. For invoice-specific notes, use the footer note field in **Settings → Shop Settings → Step 3 (System Preferences)**.

---

*End of Documentation*

*For technical support, contact your system administrator or the development team.*
