# Generated from Selenium IDE
# Test name: t04 - loglookup function
import pytest
import time
import json
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait

class Test_04_loglookup_function:
  def setup_method(self, method):
    self.driver = self.selectedBrowser
    self.vars = {}
  def teardown_method(self, method):
    self.driver.quit()

  def test_04_loglookup_function(self):
    self.driver.get("http://127.0.0.1/")
    self.driver.find_element(By.LINK_TEXT, "My Projects").click()
    self.driver.execute_script("if($('#table-proj_table').text().indexOf('Extra Calculation Functions Test')>-1) document.body.setAttribute('data-hasproj',1)")
    assert len(self.driver.find_elements(By.CSS_SELECTOR, "body[data-hasproj]")) > 0
    self.driver.find_element(By.LINK_TEXT, "Extra Calculation Functions Test").click()
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"online_designer.php\"]").click()
    self.driver.find_element(By.LINK_TEXT, "Basic Demography Form").click()
    self.driver.find_element(By.ID, "btn-first_name-sh-f").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.visibility_of_element_located((By.ID, "field_type")))
    self.driver.find_element(By.ID, "field_type").find_element(By.CSS_SELECTOR, "*[value='text']").click()
    self.driver.find_element(By.ID, "field_name").send_keys("test_loglookup1")
    self.driver.execute_script("$('#field_annotation').val('@CALCTEXT(loglookup(\\'first-user\\',\\'height\\',\\'1\\'))')")
    self.driver.find_element(By.CSS_SELECTOR, "button[style*=\"bold\"]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "test_loglookup1-tr")))
    time.sleep(1)
    self.driver.find_element(By.ID, "btn-first_name-sh-f").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.visibility_of_element_located((By.ID, "field_type")))
    self.driver.find_element(By.ID, "field_type").find_element(By.CSS_SELECTOR, "*[value='text']").click()
    self.driver.find_element(By.ID, "field_name").send_keys("test_loglookup2")
    self.driver.execute_script("$('#field_annotation').val('@CALCTEXT(left(loglookup(\\'first-date\\',\\'height\\',\\'1\\'),7))')")
    self.driver.find_element(By.CSS_SELECTOR, "button[style*=\"bold\"]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "test_loglookup2-tr")))
    time.sleep(1)
    self.driver.find_element(By.LINK_TEXT, "Record Status Dashboard").click()
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"DataEntry/index.php\"]:not([href*=\"id=&\"])").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.NAME, "first_name")))
    self.vars["username"] = self.driver.find_element(By.ID, "username-reference").text
    self.vars["yearMonth"] = self.driver.execute_script("return new Date().toISOString().substring(0,7)")
    assert self.driver.find_element(By.NAME, "test_loglookup1").get_attribute("value") == self.vars["username"]
    assert self.driver.find_element(By.NAME, "test_loglookup2").get_attribute("value") == self.vars["yearMonth"]
