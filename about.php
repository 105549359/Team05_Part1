<?php
session_start();
$currentPage = 'about';
include('header.inc');
?>

<main>
    <section class="hero">
        <div class="container">
            <h2>About CDS Solutions</h2>
            <p>Innovative Technology Solutions for Tomorrow's Challenges</p>
        </div>
    </section>

    <section class="about-section">
        <div class="about-content">
            <div class="group-info">
                <h2>Group Information</h2>
                <ul class="nested-list">
                    <li>Group Name: Group Neutral
                        <ul>
                            <li>Class Details:
                                <ul>
                                    <li>Swinburne University of Technology</li>
                                    <li>School of Science, Computing and Engineering Technologies</li>
                                    <li>COS10026 Web Technology Project</li>
                                    <li>Applied Web Project Part 1 of 2</li>
                                    <li>Semester 1, 2025</li>
                                    <li>Time: 2.30 PM to 4.30 PM</li>
                                    <li>Location: ATC Building, 325</li>
                                </ul>
                            </li>
                            <li>Team Members:
                                <ul>
                                    <li>105549359 - Chanuth Senviru</li>
                                    <li>105507535 - Chamath Lakshith</li>
                                    <li>105299159 - Ravindu Dilshan</li>
                                    <li>105305593 - Shehan Mithakshana</li>
                                </ul>
                            </li>
                            <li>Tutor: Mr. Rahul Raghavan</li>
                        </ul>
                    </li>
                </ul>
            </div>

            <figure class="group-photo">
                <img src="images/group-photo.jpg" alt="CDS Solutions Team" width="300">
                <figcaption>CDS Solutions Team Members (Left to Right): Chanuth, Chamath, Shehan, Ravindu</figcaption>
            </figure>

            <div class="member-contributions">
                <h2>Member Contributions</h2>
                <div class="contributions-grid">
                    <div class="contribution-card">
                        <div class="member-header">
                            <h3>Chanuth Senviru</h3>
                            <span class="member-id">ID: 105549359</span>
                        </div>
                        <div class="contribution-content">
                            <div class="responsibilities-section">
                                <h4>Primary Responsibilities</h4>
                                <ul class="responsibilities-list">
                                    <li>HTML to PHP Conversion - Home Page</li>
                                    <li>Template Integration</li>
                                    <li>Navigation System Implementation</li>
                                    <li>Database Integration for Manager Access</li>
                                </ul>
                            </div>
                            <div class="additional-tasks">
                                <h4>Additional Contributions</h4>
                                <ul class="responsibilities-list">
                                    <li>Code Documentation</li>
                                    <li>Cross-browser Testing</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="contribution-card">
                        <div class="member-header">
                            <h3>Shehan Mithakshana</h3>
                            <span class="member-id">ID: 105305593</span>
                        </div>
                        <div class="contribution-content">
                            <div class="responsibilities-section">
                                <h4>Primary Responsibilities</h4>
                                <ul class="responsibilities-list">
                                    <li>HTML to PHP Conversion - About Page</li>
                                    <li>Database Configuration Setup</li>
                                    <li>MySQL Connection Implementation</li>
                                    <li>Template System Development</li>
                                </ul>
                            </div>
                            <div class="additional-tasks">
                                <h4>Additional Contributions</h4>
                                <ul class="responsibilities-list">
                                    <li>UI/UX Improvements</li>
                                    <li>Accessibility Testing</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="contribution-card">
                        <div class="member-header">
                            <h3>Ravindu Dilshan</h3>
                            <span class="member-id">ID: 105299159</span>
                        </div>
                        <div class="contribution-content">
                            <div class="responsibilities-section">
                                <h4>Primary Responsibilities</h4>
                                <ul class="responsibilities-list">
                                    <li>HTML to PHP Conversion - Jobs Page</li>
                                    <li>Database Schema Design</li>
                                    <li>Job Listings Database Integration</li>
                                    <li>PHP Modular Structure Implementation</li>
                                </ul>
                            </div>
                            <div class="additional-tasks">
                                <h4>Additional Contributions</h4>
                                <ul class="responsibilities-list">
                                    <li>Job Listings Implementation</li>
                                    <li>Content Organization</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="contribution-card">
                        <div class="member-header">
                            <h3>Chamath Lakshith</h3>
                            <span class="member-id">ID: 105507535</span>
                        </div>
                        <div class="contribution-content">
                            <div class="responsibilities-section">
                                <h4>Primary Responsibilities</h4>
                                <ul class="responsibilities-list">
                                    <li>HTML to PHP Conversion - Apply Page</li>
                                    <li>Form Processing with PHP</li>
                                    <li>Database Operations Implementation</li>
                                    <li>EOI Data Management System</li>
                                </ul>
                            </div>
                            <div class="additional-tasks">
                                <h4>Additional Contributions</h4>
                                <ul class="responsibilities-list">
                                    <li>Error Handling</li>
                                    <li>Form Accessibility</li>
                                </ul>
                            </div>
                        </div>
                </div>
                </div>
            </div>

            <div class="team-profile">
                <h2>Team Profile</h2>
                <table class="team-table">
                    <caption>Team Members' Skills and Interests</caption>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Programming Skills</th>
                            <th colspan="2">Personal Interests</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="2">Chanuth Senviru</td>
                            <td>HTML5, CSS3</td>
                            <td>Web Development</td>
                            <td>UI Design</td>
                        </tr>
                        <tr>
                            <td>JavaScript, PHP</td>
                            <td colspan="2">Full Stack Development</td>
                        </tr>
                        <tr>
                            <td>Chamath Lakshith</td>
                            <td>React, Node.js</td>
                            <td>Database Design</td>
                            <td>System Architecture</td>
                        </tr>
                        <tr>
                            <td>Ravindu Dilshan</td>
                            <td>Python, Java</td>
                            <td colspan="2">Cloud Computing, DevOps</td>
                        </tr>
                        <tr>
                            <td>Shehan Mithakshana</td>
                            <td>Angular, TypeScript</td>
                            <td>API Development</td>
                            <td>Testing</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="additional-info">
                <h2>Additional Information</h2>
                
                <div class="info-section">
                    <h3>Team Demographics</h3>
                    <ul class="info-list">
                        <li>Age Range: 20-25 years</li>
                        <li>Cultural Backgrounds: Sri Lankan</li>
                        <li>Languages: English, Sinhala</li>
                    </ul>
                </div>

                <div class="info-section">
                    <h3>Hometown Information</h3>
                    <ul class="info-list">
                        <li>Chanuth - Colombo, Sri Lanka</li>
                        <li>Chamath - Galle, Sri Lanka</li>
                        <li>Ravindu - Kandy, Sri Lanka</li>
                        <li>Shehan - Matara, Sri Lanka</li>
                    </ul>
                </div>

                <div class="info-section">
                    <h3>Personal Interests</h3>
                    <ul class="info-list">
                        <li>Chanuth: Web Development, Gaming, AI/ML</li>
                        <li>Chamath: System Design, Cloud Computing, Reading</li>
                        <li>Ravindu: DevOps, Coding, Music</li>
                        <li>Shehan: Mobile Development, Sports, Photography</li>
                    </ul>
                </div>

                <div class="info-section">
                    <h3>Technical Expertise</h3>
                    <ul class="info-list">
                        <li>Frontend Development: HTML5, CSS3, JavaScript, React</li>
                        <li>Backend Development: Node.js, Python, Java</li>
                        <li>Database Management: MySQL, MongoDB</li>
                        <li>Version Control: Git, GitHub</li>
                    </ul>
                    </div>
            </div>
        </div>
    </section>
</main>

<?php include('footer.inc'); ?> 