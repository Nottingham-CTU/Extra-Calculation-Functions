# Generated from Selenium IDE
# Test name: teardown
import pytest
import time
import json
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait

class Test_teardown:
  def setup_method(self, method):
    self.driver = self.selectedBrowser
    self.vars = {}
  def teardown_method(self, method):
    self.driver.quit()

  def test_teardown(self):
    self.driver.get("http://127.0.0.1/")
    self.driver.find_element(By.LINK_TEXT, "My Projects").click()
    self.driver.execute_script("if($('#table-proj_table').text().indexOf('Extra Calculation Functions Test')>-1) document.body.setAttribute('data-hasproj',1)")
    assert len(self.driver.find_elements(By.CSS_SELECTOR, "body[data-hasproj]")) > 0
    self.driver.find_element(By.LINK_TEXT, "Extra Calculation Functions Test").click()
    self.driver.find_element(By.LINK_TEXT, "Other Functionality").click()
    self.driver.find_element(By.CSS_SELECTOR, "#row_delete_project .btn-danger").click()
    self.driver.find_element(By.ID, "delete_project_confirm").send_keys("DELETE")
    self.driver.find_element(By.CSS_SELECTOR, ".ui-dialog-buttonset > .ui-button:nth-child(2)").click()
    self.driver.find_element(By.XPATH, "(//button[text()=\"CONFIRM DELETION\"])").click()
    self.driver.execute_script("sessionStorage.removeItem('test-sysvardetails')")
