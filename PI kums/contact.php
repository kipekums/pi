<?php
// contact.php - Contact page
require_once "config.php";

$message = "";
$message_class = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $message_text = trim($_POST["message"]);
    
    // Simple validation
    if (empty($name) || empty($email) || empty($message_text)) {
        $message = "Please fill in all required fields.";
        $message_class = "alert-danger";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $message_class = "alert-danger";
    } else {
        // In a real application, you would send an email here
        // For demonstration, we'll just show a success message
        $message = "Thank you for your message! We'll get back to you soon.";
        $message_class = "alert-success";
        
        // Reset form fields
        $name = $email = $subject = $message_text = "";
    }
}

$page_title = "Contact Us";
include "header.php";
?>

<div class="container mt-5 mb-5">
    <h1 class="mb-4">Contact Us</h1>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message_class; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-6">
            <form method="post" action="contact.php">
                <div class="mb-3">
                    <label for="name" class="form-label">Your Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>">
                </div>
                
                <div class="mb-3">
                    <label for="message" class="form-label">Your Message *</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($message_text) ? htmlspecialchars($message_text) : ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">Visit Us</h3>
                    <p><i class="bi bi-geo-alt-fill text-primary me-2"></i> 123 Coffee Street, Downtown<br>City, State 12345</p>
                    
                    <h3 class="card-title mt-4">Hours</h3>
                    <p><i class="bi bi-clock-fill text-primary me-2"></i> Monday - Friday: 7:00 AM - 8:00 PM<br>
                    Saturday - Sunday: 8:00 AM - 9:00 PM</p>
                    
                    <h3 class="card-title mt-4">Contact Info</h3>
                    <p><i class="bi bi-telephone-fill text-primary me-2"></i> (555) 123-4567</p>
                    <p><i class="bi bi-envelope-fill text-primary me-2"></i> info@<?php echo strtolower(str_replace(' ', '', SITE_NAME)); ?>.com</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Connect With Us</h3>
                    <div class="social-icons fs-3">
                        <a href="#" class="text-decoration-none me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-decoration-none me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-decoration-none me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-decoration-none"><i class="bi bi-yelp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Find Us</h3>
            <!-- Replace with your actual Google Maps embed code -->
            <div class="ratio ratio-21x9">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345.678901234567!2d-74.00!3d40.70!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDQyJzAwLjAiTiA3NMKwMDAnMDAuMCJX!5e0!3m2!1sen!2sus!4v1600000000000!5m2!1sen!2sus" 
                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>