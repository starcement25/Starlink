import React, { useEffect, useRef } from 'react';
import {
  Modal,
  View,
  Text,
  TouchableOpacity,
  Image,
  Animated,
  Dimensions,
  StyleSheet,
} from 'react-native';

const { width, height } = Dimensions.get('window');

const BirthdayPopup = ({ visible, userName,birthData, onClose }) => {
  // Animation values
  const scaleAnim = useRef(new Animated.Value(0)).current;
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const pulseAnim = useRef(new Animated.Value(1)).current;
  const shakeAnim = useRef(new Animated.Value(0)).current;
  const rotateAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    if (visible) {
      startEnterAnimation();
    } else {
      resetAnimations();
    }
  }, [visible]);

  const startEnterAnimation = () => {
    // Reset all animations
    scaleAnim.setValue(0);
    fadeAnim.setValue(0);
    pulseAnim.setValue(1);
    shakeAnim.setValue(0);
    rotateAnim.setValue(0);

    // Entry animation - Scale and Fade in with bounce
    Animated.parallel([
      Animated.spring(scaleAnim, {
        toValue: 1,
        tension: 40,
        friction: 6,
        useNativeDriver: true,
      }),
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 500,
        useNativeDriver: true,
      }),
    ]).start(() => {
      // After entry, start attention-grabbing animations
      startAttentionAnimation();
    });
  };

  const startAttentionAnimation = () => {
    // Combine multiple attention effects
    
    // 1. Pulse effect (gentle breathing)
    Animated.loop(
      Animated.sequence([
        Animated.timing(pulseAnim, {
          toValue: 1.05,
          duration: 1000,
          useNativeDriver: true,
        }),
        Animated.timing(pulseAnim, {
          toValue: 1,
          duration: 1000,
          useNativeDriver: true,
        }),
      ])
    ).start();

    // 2. Gentle shake/wiggle effect
    Animated.loop(
      Animated.sequence([
        Animated.timing(shakeAnim, {
          toValue: 3,
          duration: 100,
          useNativeDriver: true,
        }),
        Animated.timing(shakeAnim, {
          toValue: -3,
          duration: 100,
          useNativeDriver: true,
        }),
        Animated.timing(shakeAnim, {
          toValue: 2,
          duration: 100,
          useNativeDriver: true,
        }),
        Animated.timing(shakeAnim, {
          toValue: -2,
          duration: 100,
          useNativeDriver: true,
        }),
        Animated.timing(shakeAnim, {
          toValue: 0,
          duration: 100,
          useNativeDriver: true,
        }),
        // Pause before next shake
        Animated.delay(3000),
      ])
    ).start();

    // 3. Subtle rotation effect
    Animated.loop(
      Animated.sequence([
        Animated.timing(rotateAnim, {
          toValue: 1,
          duration: 2000,
          useNativeDriver: true,
        }),
        Animated.timing(rotateAnim, {
          toValue: -1,
          duration: 2000,
          useNativeDriver: true,
        }),
        Animated.timing(rotateAnim, {
          toValue: 0,
          duration: 2000,
          useNativeDriver: true,
        }),
      ])
    ).start();
  };

  const handleClose = () => {
    // Exit animation - Scale down and fade out
    Animated.parallel([
      Animated.timing(scaleAnim, {
        toValue: 0,
        duration: 300,
        useNativeDriver: true,
      }),
      Animated.timing(fadeAnim, {
        toValue: 0,
        duration: 300,
        useNativeDriver: true,
      }),
    ]).start(() => {
      onClose();
    });
  };

  const resetAnimations = () => {
    scaleAnim.setValue(0);
    fadeAnim.setValue(0);
    pulseAnim.setValue(1);
    shakeAnim.setValue(0);
    rotateAnim.setValue(0);
  };

  const rotateInterpolate = rotateAnim.interpolate({
    inputRange: [-1, 0, 1],
    outputRange: ['-2deg', '0deg', '2deg'],
  });

  return (
    <Modal
      animationType="none"
      transparent={true}
      visible={visible}
      onRequestClose={handleClose}
    >
      <View style={styles.overlay}>
        <Animated.View
          style={[
            styles.container,
            {
              opacity: fadeAnim,
              transform: [
                { scale: scaleAnim },
                { scale: pulseAnim },
                { translateX: shakeAnim },
                { rotate: rotateInterpolate },
              ],
            },
          ]}
        >
          {/* Background Image */}
          <Image
            source={birthData?.img ? {uri: birthData?.img}: require('../../../assets/Bg.png')}
            style={styles.backgroundImage}
            resizeMode="cover"
          />

          {/* Close Button */}
          <TouchableOpacity style={styles.closeButton} onPress={handleClose}>
            <View style={styles.closeButtonInner}>
              <Text style={styles.closeButtonText}>✕</Text>
            </View>
          </TouchableOpacity>

          {/* Text Content Overlay */}
          <View style={styles.contentContainer}>
            <Text style={styles.title}>{birthData?.title ?? "Happy Birthday!"}</Text>
            <Text style={styles.name}>{birthData?.user_name || ''}</Text>
            <Text style={styles.message}>
              { birthData?.message ?? `May the year ahead bring you success,good health, and happiness.`}
            </Text>
            <Text style={styles.footer}>{birthData?.fromField ?? "~ Star Cement Family"}</Text>
          </View>
        </Animated.View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.75)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  container: {
    width: width * 0.85,
    height: width * 1.1, // Adjust based on your image aspect ratio
    borderRadius: 20,
    overflow: 'hidden',
    elevation: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.6,
    shadowRadius: 25,
  },
  backgroundImage: {
    width: '100%',
    height: '100%',
    position: 'absolute',
  },
  closeButton: {
    position: 'absolute',
    top: 15,
    right: 15,
    zIndex: 10,
  },
  closeButtonInner: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: 'rgba(0, 0, 0, 0.4)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  closeButtonText: {
    color: '#FFF',
    fontSize: 20,
    fontWeight: 'bold',
  },
  contentContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 30,
    paddingVertical: 40,
  },
  title: {
    fontSize: 38,
    fontWeight: 'bold',
    color: '#FFF',
    fontFamily: 'serif',
    marginBottom: 12,
    textAlign: 'center',
    textShadowColor: 'rgba(0, 0, 0, 0.5)',
    textShadowOffset: { width: 3, height: 3 },
    textShadowRadius: 8,
  },
  name: {
    fontSize: 26,
    fontWeight: '700',
    color: '#FFF',
    marginBottom: 20,
    textAlign: 'center',
    textShadowColor: 'rgba(0, 0, 0, 0.4)',
    textShadowOffset: { width: 2, height: 2 },
    textShadowRadius: 6,
  },
  message: {
    fontSize: 15,
    color: '#FFF',
    textAlign: 'center',
    lineHeight: 24,
    marginBottom: 30,
    textShadowColor: 'rgba(0, 0, 0, 0.5)',
    textShadowOffset: { width: 1, height: 1 },
    textShadowRadius: 4,
  },
  footer: {
    fontSize: 15,
    color: '#FFF',
    fontStyle: 'italic',
    textAlign: 'center',
    textShadowColor: 'rgba(0, 0, 0, 0.5)',
    textShadowOffset: { width: 1, height: 1 },
    textShadowRadius: 4,
  },
});

export default BirthdayPopup;