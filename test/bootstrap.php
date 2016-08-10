<?php

    error_reporting(E_ALL);
    
    // Disable garbage collection
    // https://scrutinizer-ci.com/blog/composer-gc-performance-who-is-affected-too
    gc_disable();
    
    // Load composer dependencies
    require __DIR__ . '/../vendor/autoload.php';
    
    // We want to compare all headers except for the User-Agent because it's based
    // on the local php and curl versions which can change.
    \VCR\VCR::configure()->addRequestMatcher('custom_headers', function(\VCR\Request $first, \VCR\Request $second){
        $firstHeaders = $first->getHeaders();
        $secondHeaders = $second->getHeaders();
        unset($firstHeaders['User-Agent']);
        unset($secondHeaders['User-Agent']);
        return count(array_diff_assoc($firstHeaders, $secondHeaders)) === 0;
    });
    
    // Configure how live requests are compared against recorded tests and 
    // determined to be the same.
    \VCR\VCR::configure()->enableRequestMatchers(array('method','url','query_string','body','custom_headers'));
    
    // This tells PHP-VCR to record a test if there is no previous recording. If there
    // is a recording then PHP-VCR will compare requests against those stored in the recording.
    \VCR\VCR::configure()->setMode('once');
    
    // Set the fixtures directory. This is where PHP-VCR will store recorded tests
    \VCR\VCR::configure()->setCassettePath('test/fixtures');
    
    // Only add curl hooks to the SDK src
    //\VCR\VCR::configure()->setWhiteList(['src']);
    \VCR\VCR::configure()->setBlackList(['vendor']);
    \VCR\VCR::configure()->enableLibraryHooks(['curl']);