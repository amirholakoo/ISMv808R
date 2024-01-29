📘 ISMv808 Factory Management System Documentation
==================================================

🌟 System Overview
------------------

ISMv808 is an integrated management system designed for a paper packaging company. It is tailored to streamline factory operations by managing shipments, inventory, sales, and purchases, all through a user-friendly web interface.

🛠️ Architectural Design
------------------------

### Server and Database

-   LAMP Stack: Linux, Apache, MySQL, and PHP.
-   Database Design: Relational database model with tables for `Trucks`, `Suppliers`, `Customers`, `RawMaterials`, `Products`, `Sales`, `Purchases`, and `Shipments`.

### Web Interface

-   Front-End: HTML, CSS, and JavaScript.
-   Back-End: PHP scripts for server-side logic.
-   AJAX: Used for dynamic content loading without page refreshes.

📄 Detailed Page Functionalities
--------------------------------

### Create Shipment Page

-   URL: `/create_shipment.php`
-   Functionality: Manage and log details of outgoing and incoming shipments.
-   Key Features:
    -   Dropdowns for selecting trucks and materials.
    -   Fields to enter shipment details.
    -   AJAX calls to dynamically load material details based on selected supplier.
-   Database Interactions:
    -   Inserts new records into `Shipments`.
    -   Updates `Trucks` status and location.
    -   Updates `RawMaterials` and `Products` based on shipment details.

### Purchase Order Page

-   URL: `/create_purchase_order.php`
-   Functionality: Process and record purchase orders for incoming shipments.
-   Key Features:
    -   Auto-fill shipment details upon selection.
    -   Fields for entering purchase order specifics like price, VAT, etc.
    -   Checkbox for approvals and validations.
-   Database Interactions:
    -   Inserts new records into `Purchases`.
    -   Updates corresponding `Shipments` record.
    -   Updates `Trucks` status and location.
    -   Reflects changes in inventory in `RawMaterials`.

### Sales Invoice Page

-   URL: `/create_sales_invoice.php`
-   Functionality: Generate invoices for sales associated with outgoing shipments.
-   Key Features:
    -   Dropdown to select customers.
    -   Auto-populated fields with shipment details.
    -   Calculation of total price, VAT, and shipping costs.
-   Database Interactions:
    -   Inserts new records into `Sales`.
    -   Updates `Shipments` with sales details.
    -   Updates `Trucks` status and location.
    -   Marks `Products` (reels) as delivered.

🔍 Important Considerations
---------------------------

### Scalability

-   Designed to handle increased data volume and user load.

### Security

-   Input validation and sanitization to prevent SQL injection.
-   User authentication and authorization (not currently implemented but recommended).

### Error Handling

-   Robust error handling in PHP scripts to manage database transaction failures.

### Backup and Recovery

-   Regular database backups and a clear recovery plan.

### Future Enhancements

-   Implementing user accounts and access levels.
-   Integration with real-time tracking systems for shipments.
