<?php
$Title = '';
$token = '';
$StartDate = null;
$EndDate = null;
$TimeZone = null;
$Description = '';
$EventImageURL = '';
$eventURL = '';
if (false === empty($details['data']['getEventDetails']['event']['EventInfo'])) {
    extract($details['data']['getEventDetails']['event']['EventInfo'], EXTR_OVERWRITE);
    $token = preg_replace('/[^a-zA-Z0-9_-]/', '-', str_replace(' ', '_', $Title));
    if (72 < strlen($token)) {
        $token = substr($token, 0, 72) . '---';
    }
    $eventURL = 'https://eventchain.io/event-details/' . implode('/', array($id, $token));

    $Description = preg_replace('%<[^>]+>%i', '', urldecode($Description));
    if (230 < strlen($Description)) {
        $Description = substr($Description, 0, 230) . '... <a class="more" href="'.$eventURL.'" target="_blank">Read More</a>';
    }
}

?>
<article class="eventchain_event">
    <section class="content">
        <div class="event_titles">
            <h2><?php echo $Title; ?></h2>
            <h3></h3>
        </div>
        <img class="event_image" src="<?php echo $EventImageURL; ?>" alt="Event Image" title="<?php echo htmlspecialchars($Title); ?>" />
        <div class="event_desc">
            <?php echo $Description; ?>
        </div>
    </section>
    <section class="button">
        <a href="<?php echo $eventURL; ?>" target="_blank">Get Tickets</a>
        <aside>
            <a href="https://eventchain.io" target="_blank">Powered by EventChain</a>
        </aside>
    </section>
</article>
