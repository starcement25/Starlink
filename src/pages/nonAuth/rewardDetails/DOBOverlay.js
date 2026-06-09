import React, { memo, useState } from "react";
import {
  Modal,
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  Platform,
} from "react-native";
import DateTimePicker from "@react-native-community/datetimepicker";
import Icon from "react-native-vector-icons/Feather";

const DOBOverlay =  memo(({ visible, onClose, onConfirm }) => {
  const [date, setDate] = useState(new Date());
  const [showPicker, setShowPicker] = useState(false);

  const onChange = (event, selectedDate) => {
    setShowPicker(Platform.OS === "ios");
    if (selectedDate) {
      setDate(selectedDate);
    }
  };

  const formatDate = (date) => {
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  return (
    <Modal visible={visible} transparent animationType="fade">
      <View style={styles.overlay}>
        <View style={styles.card}>
          <Text style={styles.smallText}>
            Your Date of Birth is not Updated
          </Text>

          <Text style={styles.title}>Kindly choose your DOB</Text>

          <TouchableOpacity
            style={styles.dateInput}
            onPress={() => setShowPicker(true)}
          >
            <Icon name="calendar" size={18} color="#888" />
            <Text style={styles.dateText}>{formatDate(date)}</Text>
            <Icon name="chevron-right" size={18} color="#888" />
          </TouchableOpacity>

          {showPicker && (
            <DateTimePicker
              value={date}
              mode="date"
              display="default"
              maximumDate={new Date()}
              onChange={onChange}
            />
          )}

          <TouchableOpacity
            style={styles.confirmBtn}
            onPress={() => {
              onConfirm(date);
              onClose();
            }}
          >
            <Text style={styles.confirmText}>Confirm</Text>
          </TouchableOpacity>
        </View>
      </View>
    </Modal>
  );
});

export default DOBOverlay;

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: "rgba(0,0,0,0.5)",
    justifyContent: "center",
    alignItems: "center",
  },
  card: {
    width: "85%",
    backgroundColor: "#fff",
    borderRadius: 16,
    padding: 20,
  },
  smallText: {
    fontSize: 13,
    color: "#555",
  },
  title: {
    fontSize: 16,
    fontWeight: "600",
    marginVertical: 8,
  },
  dateInput: {
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#ddd",
    borderRadius: 10,
    padding: 14,
    marginTop: 10,
    justifyContent: "space-between",
  },
  dateText: {
    flex: 1,
    marginLeft: 10,
    color: "#333",
  },
  confirmBtn: {
    backgroundColor: "#E31E24",
    padding: 14,
    borderRadius: 10,
    alignItems: "center",
    marginTop: 20,
  },
  confirmText: {
    color: "#fff",
    fontWeight: "600",
    fontSize: 15,
  },
});
