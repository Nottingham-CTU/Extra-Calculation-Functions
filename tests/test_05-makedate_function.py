# Generated from Selenium IDE
# Test name: t05 - makedate function
import pytest
import time
import json
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait

class Test_05_makedate_function:
  def setup_method(self, method):
    self.driver = self.selectedBrowser
    self.vars = {}
  def teardown_method(self, method):
    self.driver.quit()

  def test_05_makedate_function(self):
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
    self.driver.find_element(By.ID, "field_name").send_keys("test_makedate")
    self.driver.find_element(By.ID, "val_type").find_element(By.CSS_SELECTOR, "*[value='date_dmy']").click()
    self.driver.execute_script("$('#field_annotation').val('@CALCTEXT(makedate(\\'dmy\\',2000,1,1))')")
    self.driver.find_element(By.CSS_SELECTOR, "button[style*=\"bold\"]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "test_makedate-tr")))
    time.sleep(1)
    self.driver.find_element(By.LINK_TEXT, "Record Status Dashboard").click()
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"DataEntry/index.php\"]:not([href*=\"id=&\"])").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.NAME, "test_makedate")))
    assert self.driver.find_element(By.NAME, "test_makedate").get_attribute("value") == "01-01-2000"
    assert len(self.driver.find_elements(By.CSS_SELECTOR, "input[name=\"test_makedate\"][class*=\"calcChanged\"]")) > 0
    self.driver.execute_script("$('#south').remove();dataEntrySubmit('submit-btn-savecontinue')")
    WebDriverWait(self.driver, 60).until(expected_conditions.presence_of_element_located((By.ID, "south")))
    assert self.driver.find_element(By.NAME, "test_makedate").get_attribute("value") == "01-01-2000"
    assert len(self.driver.find_elements(By.CSS_SELECTOR, "input[name=\"test_makedate\"][class*=\"calcChanged\"]")) == 0
