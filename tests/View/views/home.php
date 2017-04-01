<!DOCTYPE html>
<?php
/** @var \Wandu\View\Phiew\Template $this */
$this->layout('layout.php', ['title' => 'title from home.php']);
?>

// this is style 1
<?php $this->push('styles') ?>
    <style data-name="1"></style>
<?php $this->endpush(); ?>

// this is style 2
<?php $this->push('styles') ?>
    <style data-name="2"></style>
<?php $this->endpush(); ?>

// this is script 1
<?php $this->push('scripts') ?>
<script data-name="1"></script>
<?php $this->endpush(); ?>

// this is script 2
<?php $this->push('scripts') ?>
<script data-name="2"></script>
<?php $this->endpush(); ?>

// contents replaced
<?php $this->section('contents') ?>
    // never..
<?php $this->endsection(); ?>

// contents
<?php $this->section('contents') ?>
<main>title = <?=$title?>, <?=$this->render('hello-stranger.php', ['who' => 'wandu'])?></main>
<?php $this->endsection(); ?>
