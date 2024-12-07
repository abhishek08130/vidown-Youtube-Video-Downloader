<?php
// Disable error reporting for production
error_reporting(0);

// Function to validate YouTube URL
function isValidYouTubeUrl($url) {
    $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[\w-]{11}$/';
    return preg_match($pattern, $url);
}

// Check if URL is provided
if (isset($_POST['url']) && !empty($_POST['url'])) {
    $url = $_POST['url'];
    
    // Validate YouTube URL
    if (!isValidYouTubeUrl($url)) {
        echo json_encode(['error' => 'Invalid YouTube URL']);
        exit;
    }

    try {
        // Extract video ID from URL
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n]+)/', $url, $matches);
        $videoId = $matches[1];

        // Get video info using YouTube Data API
        $apiKey = 'YOUR_YOUTUBE_API_KEY'; // Replace with your actual API key
        $apiUrl = "https://www.googleapis.com/youtube/v3/videos?id=$videoId&key=$apiKey&part=snippet";
        
        $response = file_get_contents($apiUrl);
        $videoData = json_decode($response, true);

        if (empty($videoData['items'])) {
            echo json_encode(['error' => 'Video not found']);
            exit;
        }

        // Get video title
        $videoTitle = $videoData['items'][0]['snippet']['title'];
        
        // Generate download links for different formats
        $formats = [
            'mp4' => "https://www.youtube.com/watch?v=$videoId",
            'webm' => "https://www.youtube.com/watch?v=$videoId"
        ];

        echo json_encode([
            'success' => true,
            'title' => $videoTitle,
            'formats' => $formats
        ]);

    } catch (Exception $e) {
        echo json_encode(['error' => 'An error occurred while processing your request']);
    }
} else {
    echo json_encode(['error' => 'No URL provided']);
}
?>
