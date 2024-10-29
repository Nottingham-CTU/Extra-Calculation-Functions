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

class TestT03ifnullfunction():
  def setup_method(self, method):
    self.driver = self.selectedBrowser
    self.vars = {}

  def teardown_method(self, method):
    self.driver.quit()

  def test_t03ifnullfunction(self):
    self.driver.get("http://127.0.0.1/")
    self.driver.find_element(By.LINK_TEXT, "My Projects").click()
    self.driver.execute_script("if($(\'#table-proj_table\').text().indexOf(\'Extra Calculation Functions Test\')>-1) document.body.setAttribute(\'data-hasproj\',1)")
    elements = self.driver.find_elements(By.CSS_SELECTOR, "body[data-hasproj]")
    assert len(elements) > 0
    self.driver.find_element(By.LINK_TEXT, "Extra Calculation Functions Test").click()
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"online_designer.php\"]").click()
    self.driver.find_element(By.LINK_TEXT, "Basic Demography Form").click()
    self.driver.find_element(By.ID, "btn-first_name-sh-f").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.visibility_of_element_located((By.ID, "field_type")))
    dropdown = self.driver.find_element(By.ID, "field_type")
    dropdown.find_element(By.CSS_SELECTOR, "*[value='text']").click()
    self.driver.find_element(By.ID, "field_name").send_keys("test_ifnull")
    self.driver.execute_script("$(\'#field_annotation\').val(\'@CALCTEXT(ifnull([weight],[height])))\')")
    self.driver.find_element(By.CSS_SELECTOR, "button[style*=\"bold\"]").click()
    time.sleep(2)
    self.driver.find_element(By.LINK_TEXT, "Record Status Dashboard").click()
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"DataEntry/index.php\"]:not([href*=\"id=&\"])").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.NAME, "weight")))
    self.driver.execute_script("$(\'[name=\"weight\"]\').val(\'\');$(\'[name=\"height\"]\').val(\'165\');calculate();$(\'body\').attr(\'data-donecalc\',\'1\')")
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.CSS_SELECTOR, "body[data-donecalc=\"1\"]")))
    value = self.driver.find_element(By.NAME, "test_ifnull").get_attribute("value")
    assert value == "165"
    self.driver.execute_script("$(\'[name=\"weight\"]\').val(\'60\');calculate();$(\'body\').attr(\'data-donecalc\',\'2\')")
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.CSS_SELECTOR, "body[data-donecalc=\"2\"]")))
    value = self.driver.find_element(By.NAME, "test_ifnull").get_attribute("value")
    assert value == "60"

