<?php
$currentPage = 'enhancements';
include('header.inc');
?>

<div class="container">
    <?php include('nav.inc'); ?>
</div>

<main>
    <section class="hero">
        <div class="container">
            <h2>Website Enhancements</h2>
            <p>Additional features and improvements implemented in this project.</p>
        </div>
    </section>

    <section class="enhancements">
        <div class="container">
            <div class="enhancement-item">
                <h3>1. Advanced HR Manager Dashboard</h3>
                <div class="enhancement-details">
                    <h4>Description</h4>
                    <p>Enhanced HR manager interface with advanced sorting and filtering capabilities.</p>
                    
                    <h4>Features</h4>
                    <ul>
                        <li>Sort applications by:
                            <ul>
                                <li>EOI Number</li>
                                <li>Job Reference</li>
                                <li>Applicant Last Name</li>
                                <li>Application Status</li>
                            </ul>
                        </li>
                        <li>Filter applications by:
                            <ul>
                                <li>Job Reference Number</li>
                                <li>Applicant Name (First or Last)</li>
                            </ul>
                        </li>
                        <li>Bulk delete functionality for job references</li>
                    </ul>

                    <h4>Technical Implementation</h4>
                    <p>Implemented using MySQL ORDER BY and WHERE clauses with parameterized queries for security.</p>
                </div>
            </div>

            <div class="enhancement-item">
                <h3>2. Secure Authentication System</h3>
                <div class="enhancement-details">
                    <h4>Description</h4>
                    <p>Comprehensive authentication system for HR managers with security features.</p>
                    
                    <h4>Features</h4>
                    <ul>
                        <li>Secure registration system with:
                            <ul>
                                <li>Password hashing using PHP's password_hash()</li>
                                <li>Email validation</li>
                                <li>Username uniqueness check</li>
                            </ul>
                        </li>
                        <li>Enhanced login security:
                            <ul>
                                <li>Account lockout after 3 failed attempts</li>
                                <li>15-minute lockout period</li>
                                <li>Remaining attempts counter</li>
                            </ul>
                        </li>
                        <li>Session management for secure access control</li>
                    </ul>

                    <h4>Technical Implementation</h4>
                    <p>Uses PHP sessions, prepared statements for database queries, and secure password handling practices.</p>
                </div>
            </div>

            <div class="enhancement-item">
                <h3>3. Dynamic Job Management</h3>
                <div class="enhancement-details">
                    <h4>Description</h4>
                    <p>Implemented a database-driven job listing system.</p>
                    
                    <h4>Features</h4>
                    <ul>
                        <li>Jobs stored in MySQL database</li>
                        <li>Dynamic job listing page</li>
                        <li>Automatic job reference selection in application form</li>
                    </ul>

                    <h4>Technical Implementation</h4>
                    <p>Created a jobs table in MySQL and implemented dynamic content loading in PHP.</p>
                </div>
            </div>
        </div>
    </section>
</main>