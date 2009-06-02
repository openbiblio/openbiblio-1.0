<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/Bookings.php"));
  require_once(REL(__FILE__, "../classes/Date.php"));

  $bookings = new Bookings;
  $booking = $bookings->getOne($_REQUEST['bookingid']);

  $errors = array();

  foreach (array(book_dt, due_dt) as $dt) {
    if (isset($_REQUEST[$dt])) {
      list($booking[$dt], $error) = Date::read_e($_REQUEST[$dt]);
      if ($error) {
        $errors[$dt] = $error->toStr();
      }
    }
  }
  if ($_REQUEST['action_booking_mbr_add']) {
    foreach ($_REQUEST['id'] as $mbrid) {
      if (!in_array($mbrid, $booking['mbrids'])) {
        $booking['mbrids'][] = $mbrid;
      }
    }
  }
  if ($_REQUEST['action_booking_mbr_del']) {
    foreach ($_REQUEST['id'] as $mbrid) {
      if (($k = array_search($mbrid, $booking['mbrids'])) !== NULL) {
        unset($booking['mbrids'][$k]);
      }
    }
  }

  $confirm_book_dt = NULL;
  $confirm_due_dt = NULL;
  if (isset($_POST['confirm_book_dt'])) {
    $confirm_book_dt = $_POST['confirm_book_dt'];
  }
  if (isset($_POST['confirm_due_dt'])) {
    $confirm_due_dt = $_POST['confirm_due_dt'];
  }
  $confirmed=false;
  if ($booking['book_dt'] == $confirm_book_dt
      and $booking['due_dt'] == $confirm_due_dt) {
    $confirmed = true;
  }

  $ignorable = false;
  $msg = '';
  if (empty($errors)) {
    $errors = $bookings->update_el($booking, $confirmed);
  }

  $msg = '';
  if (!empty($errors)) {
    $_SESSION['postVars'] = mkPostVars();
    foreach ($errors as $e) {
      if (is_a($e, IgnorableError)) {
        $_SESSION['postVars']['confirm_book_dt'] = $booking['book_dt'];
        $_SESSION['postVars']['confirm_due_dt'] = $booking['due_dt'];
      }
    }
    list($msg, $fielderrs) = FieldError::listExtract($errors);
    $_SESSION["pageErrors"] = $fielderrs;
  }

  header('Location: ../circ/booking_view.php?bookingid='.U($booking['bookingid']).'&msg='.U($msg));
