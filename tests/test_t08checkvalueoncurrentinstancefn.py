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

class TestT08checkvalueoncurrentinstancefn():
  def setup_method(self, method):
    self.driver = self.selectedBrowser
    self.vars = {}

  def teardown_method(self, method):
    self.driver.quit()

  def test_t08checkvalueoncurrentinstancefn(self):
    self.driver.get("http://127.0.0.1/")
    self.driver.find_element(By.LINK_TEXT, "My Projects").click()
    self.driver.execute_script("if($(\'#table-proj_table\').text().indexOf(\'Extra Calculation Functions Test\')>-1) document.body.setAttribute(\'data-hasproj\',1)")
    elements = self.driver.find_elements(By.CSS_SELECTOR, "body[data-hasproj]")
    assert len(elements) > 0
    self.driver.find_element(By.LINK_TEXT, "Extra Calculation Functions Test").click()
    self.driver.find_element(By.LINK_TEXT, "Project Setup").click()
    self.driver.find_element(By.ID, "enableRepeatingFormsEventsBtn").click()
    self.driver.execute_script("$(\'#south\').remove()")
    element = self.driver.find_element(By.CSS_SELECTOR, ".repeat_form_chkbox")
    if element.is_selected() != True: element.click()
    self.driver.find_element(By.XPATH, "//button[contains(.,\"Save\")]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "south")))
    elements = self.driver.find_elements(By.CSS_SELECTOR, "#enableRepeatingFormsEventsOption[style*=\"color:green\"]")
    assert len(elements) > 0
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"Design/online_designer.php\"]").click()
    self.driver.execute_script("displayFormDisplayLogicPopup()")
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.CSS_SELECTOR, "[onchange*=\"checkRepeatSelection\"]")))
    dropdown = self.driver.find_element(By.CSS_SELECTOR, "[onchange*=\"checkRepeatSelection\"]")
    dropdown.find_element(By.XPATH, "//option[. = 'Basic Demography Form']").click()
    self.driver.execute_script("$(\'#control-condition-1\').val(\'checkvalueoncurrentinstance(\"demographics_complete\",0,false,0,false)\')")
    self.driver.execute_script("$(\'#south\').remove()")
    self.driver.find_element(By.XPATH, "//button[contains(.,\"Save\")]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "south")))
    self.driver.find_element(By.LINK_TEXT, "Record Status Dashboard").click()
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"DataEntry/index.php\"]:not([href*=\"id=&\"])").click()
    elements = self.driver.find_elements(By.NAME, "first_name")
    assert len(elements) > 0
    self.driver.find_element(By.LINK_TEXT, "Record Status Dashboard").click()
    if self.driver.execute_script("return ($(\'.dataEntryLeavePageBtn\').length > 0)"):
      self.driver.find_element(By.CSS_SELECTOR, ".dataEntryLeavePageBtn").click()
    self.driver.find_element(By.CSS_SELECTOR, "button.btnAddRptEv").click()
    elements = self.driver.find_elements(By.NAME, "first_name")
    assert len(elements) == 0
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"Design/online_designer.php\"]").click()
    self.driver.execute_script("displayFormDisplayLogicPopup()")
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.CSS_SELECTOR, "[onchange*=\"checkRepeatSelection\"]")))
    self.driver.execute_script("$(\'#control-condition-1\').val(\'checkvalueoncurrentinstance(\"demographics_complete\",2,true,0,false)\')")
    self.driver.execute_script("$(\'#south\').remove()")
    self.driver.find_element(By.XPATH, "//button[contains(.,\"Save\")]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "south")))
    self.driver.find_element(By.LINK_TEXT, "Record Status Dashboard").click()
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"DataEntry/index.php\"]:not([href*=\"id=&\"])").click()
    elements = self.driver.find_elements(By.NAME, "first_name")
    assert len(elements) == 0
    self.driver.find_element(By.LINK_TEXT, "Record Status Dashboard").click()
    self.driver.find_element(By.CSS_SELECTOR, "button.btnAddRptEv").click()
    elements = self.driver.find_elements(By.NAME, "first_name")
    assert len(elements) > 0
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"Design/online_designer.php\"]").click()
    if self.driver.execute_script("return ($(\'.dataEntryLeavePageBtn\').length > 0)"):
      self.driver.find_element(By.CSS_SELECTOR, ".dataEntryLeavePageBtn").click()
    self.driver.execute_script("displayFormDisplayLogicPopup()")
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.CSS_SELECTOR, "[onchange*=\"checkRepeatSelection\"]")))
    self.driver.execute_script("$(\'#control-condition-1\').val(\'checkvalueoncurrentinstance(\"demographics_complete\",2,true,1,false)\')")
    self.driver.execute_script("$(\'#south\').remove()")
    self.driver.find_element(By.XPATH, "//button[contains(.,\"Save\")]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "south")))
    self.driver.find_element(By.LINK_TEXT, "Record Status Dashboard").click()
    self.driver.find_element(By.CSS_SELECTOR, "button.btnAddRptEv").click()
    elements = self.driver.find_elements(By.NAME, "first_name")
    assert len(elements) == 0
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"Design/online_designer.php\"]").click()
    self.driver.execute_script("displayFormDisplayLogicPopup()")
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.CSS_SELECTOR, "[onchange*=\"checkRepeatSelection\"]")))
    self.driver.execute_script("$(\'#control-condition-1\').val(\'checkvalueoncurrentinstance(\"demographics_complete\",2,true,0,true)\')")
    self.driver.execute_script("$(\'#south\').remove()")
    self.driver.find_element(By.XPATH, "//button[contains(.,\"Save\")]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "south")))
    self.driver.find_element(By.LINK_TEXT, "Record Status Dashboard").click()
    self.driver.find_element(By.CSS_SELECTOR, "button.btnAddRptEv").click()
    elements = self.driver.find_elements(By.NAME, "first_name")
    assert len(elements) > 0
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"Design/online_designer.php\"]").click()
    if self.driver.execute_script("return ($(\'.dataEntryLeavePageBtn\').length > 0)"):
      self.driver.find_element(By.CSS_SELECTOR, ".dataEntryLeavePageBtn").click()
    self.driver.execute_script("displayFormDisplayLogicPopup()")
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.CSS_SELECTOR, "[onchange*=\"checkRepeatSelection\"]")))
    self.driver.execute_script("$(\'#control-condition-1\').val(\'checkvalueoncurrentinstance(\"demographics_complete\",0,true,0,true)\')")
    self.driver.execute_script("$(\'#south\').remove()")
    self.driver.find_element(By.XPATH, "//button[contains(.,\"Save\")]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "south")))
    self.driver.find_element(By.LINK_TEXT, "Record Status Dashboard").click()
    self.driver.find_element(By.CSS_SELECTOR, "button.btnAddRptEv").click()
    elements = self.driver.find_elements(By.NAME, "first_name")
    assert len(elements) == 0
    self.driver.find_element(By.CSS_SELECTOR, "a[href*=\"Design/online_designer.php\"]").click()
    self.driver.execute_script("displayFormDisplayLogicPopup()")
    self.driver.find_element(By.ID, "deleteAll").click()
    self.driver.switch_to.alert.accept()
    self.driver.execute_script("$(\'#south\').remove()")
    self.driver.find_element(By.XPATH, "//button[contains(.,\"Save\")]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "south")))
    self.driver.find_element(By.LINK_TEXT, "Project Setup").click()
    self.driver.find_element(By.ID, "enableRepeatingFormsEventsBtn").click()
    self.driver.execute_script("$(\'#south\').remove()")
    element = self.driver.find_element(By.CSS_SELECTOR, ".repeat_form_chkbox")
    if element.is_selected: element.click()
    self.driver.find_element(By.XPATH, "//button[contains(.,\"Save\")]").click()
    WebDriverWait(self.driver, 30).until(expected_conditions.presence_of_element_located((By.ID, "south")))
