<?php
// about.php - About Us page
require_once "config.php";

$page_title = "About Us";
include "header.php";
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-6">
            <h1 class="mb-4">About <?php echo SITE_NAME; ?></h1>
            <p class="lead">Our story begins with a passion for exceptional coffee and a dream to create a welcoming space for our community.</p>
            
            <h3 class="mt-4">Our Journey</h3>
            <p>Founded in 2015, <?php echo SITE_NAME; ?> started as a small corner café with big ambitions. What began as a modest coffee shop has grown into a beloved local establishment, serving thousands of happy customers each month.</p>
            
            <h3 class="mt-4">Our Philosophy</h3>
            <p>We believe that great coffee is an art form. From carefully selected beans to expertly trained baristas, we pay attention to every detail to ensure your coffee experience is nothing short of exceptional.</p>
            
            <h3 class="mt-4">Our Commitment</h3>
            <p>We're committed to sustainability and ethical sourcing. We work directly with coffee farmers to ensure fair compensation and environmentally responsible farming practices.</p>
        </div>
        <div class="col-md-6">
            <img src="images/about-us.jpg" alt="Our Coffee Shop" class="img-fluid rounded mb-4">
            
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Our Values</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="bi bi-check-circle-fill text-primary me-2"></i> Quality without compromise</li>
                        <li class="list-group-item"><i class="bi bi-check-circle-fill text-primary me-2"></i> Community connection</li>
                        <li class="list-group-item"><i class="bi bi-check-circle-fill text-primary me-2"></i> Environmental responsibility</li>
                        <li class="list-group-item"><i class="bi bi-check-circle-fill text-primary me-2"></i> Customer satisfaction</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Meet Our Team</h2>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <img src="images/team-1.jpg" class="card-img-top" alt="Team Member">
                <div class="card-body">
                    <h5 class="card-title">Emma Johnson</h5>
                    <p class="card-subtitle mb-2 text-muted">Founder & Head Barista</p>
                    <p class="card-text">With over 15 years of experience in specialty coffee, Emma brings passion and expertise to every cup.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <img src="images/team-2.jpg" class="card-img-top" alt="Team Member">
                <div class="card-body">
                    <h5 class="card-title">Michael Chen</h5>
                    <p class="card-subtitle mb-2 text-muted">Master Baker</p>
                    <p class="card-text">Michael creates our delicious pastries and desserts that perfectly complement our coffee selection.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <img src="images/team-3.jpg" class="card-img-top" alt="Team Member">
                <div class="card-body">
                    <h5 class="card-title">Sarah Martinez</h5>
                    <p class="card-subtitle mb-2 text-muted">Store Manager</p>
                    <p class="card-text">Sarah ensures that your visit to our café is always pleasant and memorable.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>