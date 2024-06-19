# Hospital Management System

This project is a comprehensive Hospital Management System that manages the operations of a hospital. I have used the XAMPP as database. It includes features for handling doctor, patient, prescription, admission, billing, and installment details. The system supports two types of patients: general patients who come for checkups, and patients who require admission.

## Features

### Patient Management
- **General Patients:** Manage patients who come for routine checkups.
- **Admitted Patients:** Handle patients who require hospital admission.

### Doctor Management
- **Detailed View:** View detailed information about doctors, including their specialties and schedules.
- **CRUD Operations:** Admins can create, read, update, and delete doctor records.

### Prescription Management
- **Prescription Details:** Manage and view detailed prescription information.

### Admission Management
- **Patient Admission:** Manage the admission process for patients requiring hospital stay.

### Billing and Installment Management
- **Billing:** Handle billing details for treatments and services.
- **Installment Payments:** Manage installment payments for bills.

### User Roles
- **Admin:** Has full access to all features and can perform CRUD operations on all tables.
- **General Users:** Have limited access based on their role (e.g., doctors can manage their own schedules and patient information).

### Email Authentication
- **PHP Mailer:** The system includes an email authentication feature using PHP Mailer for secure communication and verification.

## Technologies Used
- **Backend:** PHP, MySQL
- **Frontend:** HTML, CSS, Bootstrap
- **Email:** PHP Mailer for sending verification and notification emails

## Getting Started

### Prerequisites
- **Web Server:** Apache or any compatible server
- **PHP:** Version 7.4 or higher
- **MySQL:** Version 5.7 or higher
- **Composer:** For managing PHP dependencies

### Installation
1. **Clone the repository:**
   ```sh
   git clone https://github.com/TimeWithPotato/hospital-management-system.git
   ```
2. **Navigate to the project directory:**
   ```sh
   cd hospital-management-system
   ```
3. **Install dependencies:**
   ```sh
   composer install
   ```
4. **Set up the database:**
   - Import the provided SQL file (`hospital_management (6).sql`) into your MySQL database.
   - Update the `connect.php` file with your database credentials.

5. **Start the server:**
   - Use a local development server like XAMPP or MAMP, or set up a virtual host in Apache.

6. **Access the application:**
   - Open your browser and navigate to `http://localhost/hospital-management-system`

### Usage

#### Admin Panel
- **URL:** `http://localhost/hospital-management-system/admin`
- **Features:** CRUD operations for doctors, patients, prescriptions, billing, and installments.

#### User Panel
- **URL:** `http://localhost/hospital-management-system/user`
- **Features:** Limited access based on user roles. Doctors can manage their schedules and patient details.

### Email Authentication
- **Configuration:** Update the email settings in `otp-checker.php` and other relevant files with your SMTP server details.
- **Functionality:** The system sends OTP for verification during user registration and password recovery.

### Event Scheduler
- **Auto Cleanup:** The system uses MySQL Event Scheduler to delete OTP records after a set period.

## Contributing
1. **Fork the repository.**
2. **Create a new branch:**
   ```sh
   git checkout -b feature-branch
   ```
3. **Commit your changes:**
   ```sh
   git commit -m 'Add some feature'
   ```
4. **Push to the branch:**
   ```sh
   git push origin feature-branch
   ```
5. **Create a pull request.**

## License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

Developed by [Najifa](https://github.com/najifatabassum01) & [Arif](https://github.com/TimeWithPotato).
