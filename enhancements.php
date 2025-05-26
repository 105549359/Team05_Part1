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