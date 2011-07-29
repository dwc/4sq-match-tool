<?php
include_once('config.php');
include_once('functions.php');

// Default to loading information from a generic file
if (! defined('BUILDING_CSV_FILE')) {
  define('BUILDING_CSV_FILE', 'buildings.csv');
}

$errors = array();

$num = (int) isset($_GET['num']) ? $_GET['num'] : 10;
if ($num > 50) {
  $errors[] = 'Loading lots of information from foursquare; this might take a while.';
}

$start = (int) isset($_GET['start']) ? $_GET['start'] : 1;
$end = $start + $num - 1;

$buildings = array();
try {
  $buildings = load_buildings(BUILDING_CSV_FILE);
}
catch (Exception $e) {
  $errors[] = $e->getMessage();
}
?>
<!doctype html>
<html>
  <head>
    <title>foursquare match tool</title>
  </head>
  <body>
    <h1>foursquare match tool</h1>
    <?php if ($errors): ?>
      <h2>Errors</h2>
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <?php if ($buildings): ?>
      <h2>Buildings</h2>
      <p>Showing <?php echo htmlspecialchars($start); ?> - <?php echo htmlspecialchars($end); ?> of <?php echo htmlspecialchars(count($buildings)); ?> building<?php if (count($buildings) != 1): ?>s<?php endif; ?>.</p>
      <?php for ($i = $start - 1; $i < $end; $i++): $building = $buildings[$i]; ?>
        <h2><?php echo htmlspecialchars($building['name']); ?></h2>
        <?php $venues = foursquare_search_venues($building['name'], $building['lat'], $building['lng']); ?>
        <?php if ($venues): ?>
          <ul>
            <?php foreach ($venues as $venue): ?>
              <li><a href="<?php echo htmlspecialchars(foursquare_venue_url($venue)); ?>"><?php echo htmlspecialchars($venue->name); ?></a></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No matching venues found.</p>
        <?php endif; ?>
      <?php endfor; ?>

      <p><a href="?start=<?php echo htmlspecialchars($end + 1); ?>&num=<?php echo htmlspecialchars($num); ?>">I'm done with these; show me the next <?php echo htmlspecialchars($num); ?></a>.</p>
    <?php endif; ?>
  </body>
</html>
