import sys, getopt
import RPi.GPIO as GPIO
import time
import logging

constGpioPin         = 7
constFrequency       = 50
constSignalRight     = 1.8
constSignalNeutral   = 7.5
constSignalLeft      = 10.5
constDurationPress   = 3
constDurationRelease = 1

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

GPIO.setmode(GPIO.BOARD)
GPIO.setup(constGpioPin, GPIO.OUT)

p = GPIO.PWM(constGpioPin, constFrequency)
p.start(constSignalRight)

p.ChangeDutyCycle(constSignalLeft)
time.sleep(constDurationPress)
p.ChangeDutyCycle(constSignalRight)
time.sleep(constDurationRelease)

p.stop()
GPIO.cleanup()
print ("Button is pressed" )        
         
  

