# Generated by Selenium IDE
import pytest
import time
import json
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities

class TestInit():
  def setup_method(self, method):
    self.driver = self.selectedBrowser
    self.vars = {}

  def teardown_method(self, method):
    self.driver.quit()

  def test_init(self):
    self.driver.get("http://127.0.0.1/")
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"ControlCenter/index.php\"]").click()
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"ExternalModules/manager/control_center.php\"]").click()
    self.driver.find_element(By.CSS_SELECTOR, "tr[data-module=\"extra_calculation_functions\"] .external-modules-configure-button").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.NAME, "sysvar-enable")))
    if self.driver.execute_script("return ($(\'[name=\"sysvar-enable\"]:checked\').length == 0)"):
      element = self.driver.find_element(By.NAME, "sysvar-enable")
      if element.is_selected() != True: element.click()
    if self.driver.execute_script("return ($(\'input[type=\"text\"][name=\"sysvar-name____0\"]\').val() == \'\')"):
      self.driver.find_element(By.NAME, "sysvar-name____0").send_keys("examplesysvar")
    if self.driver.execute_script("return ($(\'input[type=\"text\"][name=\"sysvar-value____0\"]\').val() == \'\')"):
      self.driver.find_element(By.NAME, "sysvar-value____0").send_keys("examplesysvarvalue")
    self.driver.find_element(By.CSS_SELECTOR, "#external-modules-configure-modal .modal-footer .save").click()
    time.sleep(2)
    self.driver.find_element(By.CSS_SELECTOR, "tr[data-module=\"extra_calculation_functions\"] .external-modules-configure-button").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.NAME, "sysvar-enable")))
    elements = self.driver.find_elements(By.CSS_SELECTOR, "input[type=\"checkbox\"][name=\"sysvar-enable\"]:checked")
    assert len(elements) > 0
    elements = self.driver.find_elements(By.CSS_SELECTOR, "input[type=\"text\"][name=\"sysvar-name____0\"]:not([value=\"\"])")
    assert len(elements) > 0
    elements = self.driver.find_elements(By.CSS_SELECTOR, "input[type=\"text\"][name=\"sysvar-value____0\"]:not([value=\"\"])")
    assert len(elements) > 0
    self.driver.execute_script("sessionStorage.setItem(\'test-sysvardetails\',JSON.stringify({\"name\":$(\'[name=\"sysvar-name____0\"]\').val(),\"value\":$(\'[name=\"sysvar-value____0\"]\').val()}))")
    self.driver.find_element(By.CSS_SELECTOR, "#external-modules-configure-modal .modal-footer .save").click()
    time.sleep(2)
    self.driver.find_element(By.LINK_TEXT, "My Projects").click()
    self.driver.execute_script("if($(\'#table-proj_table\').text().indexOf(\'Extra Calculation Functions Test\')>-1) document.body.setAttribute(\'data-hasproj\',1)")
    elements = self.driver.find_elements(By.CSS_SELECTOR, "body[data-hasproj]")
    assert len(elements) == 0
    self.driver.find_element(By.LINK_TEXT, "New Project").click()
    self.driver.find_element(By.ID, "app_title").send_keys("Extra Calculation Functions Test")
    dropdown = self.driver.find_element(By.ID, "purpose")
    dropdown.find_element(By.XPATH, "//option[. = 'Practice / Just for fun']").click()
    self.driver.find_element(By.ID, "project_template_radio1").click()
    self.driver.find_element(By.CSS_SELECTOR, "input[name=\"copyof\"][value=\"5\"]").click()
    self.driver.find_element(By.CSS_SELECTOR, ".btn-primaryrc").click()
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"ExternalModules/manager/project.php\"]").click()
    self.driver.find_element(By.ID, "external-modules-enable-modules-button").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.CSS_SELECTOR, "tr[data-module=\"extra_calculation_functions\"] .enable-button")))
    self.driver.find_element(By.CSS_SELECTOR, "tr[data-module=\"extra_calculation_functions\"] .enable-button").click()
    time.sleep(5)
    elements = self.driver.find_elements(By.CSS_SELECTOR, "#external-modules-enabled tr[data-module=\"extra_calculation_functions\"]")
    assert len(elements) > 0
    self.driver.find_element(By.LINK_TEXT, "Add / Edit Records").click()
    self.driver.find_element(By.CSS_SELECTOR, "button.btn-rcgreen").click()
    self.driver.execute_script("autoFill()")
    self.driver.find_element(By.CSS_SELECTOR, "input[name=\"height\"]").send_keys("165")
    dropdown = self.driver.find_element(By.NAME, "demographics_complete")
    dropdown.find_element(By.CSS_SELECTOR, "*[value='0']").click()
    self.driver.find_element(By.ID, "submit-btn-saverecord").click()
    time.sleep(3)

