# Emoncms collector module
Module for Emoncms to collect data from passive devices. ModBus or Meter-Bus needs a master to read data from all slaves on bus.
A collector is a configuration to connect to a master interface. A device can use a collector to setup a connection.

bus slave examples: heat, water, gas or electricity meter

Modbus RTU => serial parameters
Modbus TCP => hostname/IP, port
Meter-Bus Serial => serial parameters
Meter-Bus Ethernet => hostname/IP, port
