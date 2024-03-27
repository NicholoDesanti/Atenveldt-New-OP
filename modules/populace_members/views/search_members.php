<!-- search_members.php -->
<form id="search-form" action="<?php echo BASE_URL; ?>populace_members/search_members" method="GET">
    <input type="text" id="search-input" name="q" placeholder="Search by name..." value="<?php echo htmlspecialchars($search_query); ?>" autocomplete="off">
    <button type="submit">Search</button>
</form>
<ul id="suggestions-list"></ul>

<script>
// JavaScript code for dynamic search suggestions
// Add event listener to the search input field
document.getElementById('search-input').addEventListener('input', function() {
    // Get the search query from the input field
    var query = this.value.trim();

    // Make AJAX request to fetch search suggestions
    if (query !== '') {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?php echo BASE_URL; ?>populace_members/search_suggestions?q=' + encodeURIComponent(query), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Parse the response as JSON
                var suggestions = JSON.parse(xhr.responseText);

                // Update the suggestions container with the new suggestions
                updateSuggestions(suggestions);
            }
        };
        xhr.send();
    } else {
        // Clear the suggestions container if the query is empty
        updateSuggestions([]);
    }
});

// Function to update the suggestions container with new suggestions
function updateSuggestions(suggestions) {
    var suggestionsList = document.getElementById('suggestions-list');
    suggestionsList.innerHTML = ''; // Clear existing suggestions

    // Append new suggestions to the suggestions container
    suggestions.forEach(function(suggestion) {
        var listItem = document.createElement('li');
        var link = document.createElement('a');
        link.href = '<?php echo BASE_URL; ?>populace_members/profile/' + suggestion.id;
        link.textContent = suggestion.name;
        listItem.appendChild(link);
        suggestionsList.appendChild(listItem);
    });
}

// Prevent form submission when pressing Enter key
document.getElementById('search-form').addEventListener('submit', function(event) {
    event.preventDefault();
});
</script>