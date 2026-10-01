import React, { useRef, useState, useEffect } from 'react';
import { WebView } from 'react-native-webview';
import { BackHandler, StyleSheet, View, Text, Animated, Easing, Platform, TouchableOpacity, Image } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { Colors } from '../../../helper/safeview/Colors';
import Icons from '../../../helper/image/ImageList';
import SafeView from '../../../helper/safeview/SafeView';

const SpinnerRing = ({ color }) => {
    const rotation = useRef(new Animated.Value(0)).current;

    useEffect(() => {
        Animated.loop(
            Animated.timing(rotation, {
                toValue: 1,
                duration: 1000,
                easing: Easing.linear,
                useNativeDriver: true,
            })
        ).start();
    }, []);

    const rotate = rotation.interpolate({
        inputRange: [0, 1],
        outputRange: ['0deg', '360deg'],
    });

    return (
        <Animated.View style={[styles.spinnerRing, { borderTopColor: color, transform: [{ rotate }] }]} />
    );
};

// Extract base origin from a URL (e.g. "https://admin.starsaathirewards.com")
const getOrigin = (url) => {
    try {
        const { protocol, host } = new URL(url);
        return `${protocol}//${host}`;
    } catch {
        return url;
    }
};

const Vouchers = (props) => {
    const navigation = useNavigation();
    const webViewRef = useRef(null);
    const { userId } = props?.route?.params;
    const [canGoBack, setCanGoBack] = useState(false);
    const [isLoading, setIsLoading] = useState(true);

    const fadeAnim = useRef(new Animated.Value(1)).current;
    const dot1 = useRef(new Animated.Value(0)).current;
    const dot2 = useRef(new Animated.Value(0)).current;
    const dot3 = useRef(new Animated.Value(0)).current;

    useEffect(() => {
        const a1 = animateDot(dot1, 0);
        const a2 = animateDot(dot2, 150);
        const a3 = animateDot(dot3, 300);
        a1.start(); a2.start(); a3.start();
        return () => { a1.stop(); a2.stop(); a3.stop(); };
    }, []);

    useEffect(() => {
        const backHandler = BackHandler.addEventListener(
            'hardwareBackPress',
            () => {
                if (canGoBack) {
                    webViewRef.current.goBack();
                    return true;
                } else {
                    navigation.goBack();
                    return true;
                }
            }
        );
        return () => backHandler.remove();
    }, [canGoBack]);

    const animateDot = (dot, delay) => {
        return Animated.loop(
            Animated.sequence([
                Animated.delay(delay),
                Animated.timing(dot, {
                    toValue: -10,
                    duration: 300,
                    easing: Easing.out(Easing.quad),
                    useNativeDriver: true,
                }),
                Animated.timing(dot, {
                    toValue: 0,
                    duration: 300,
                    easing: Easing.in(Easing.quad),
                    useNativeDriver: true,
                }),
                Animated.delay(600),
            ])
        );
    };

    const handleLoadEnd = () => {
        Animated.timing(fadeAnim, {
            toValue: 0,
            duration: 350,
            easing: Easing.out(Easing.ease),
            useNativeDriver: true,
        }).start(() => setIsLoading(false));
    };

    const handleError = (syntheticEvent) => {
        const { nativeEvent } = syntheticEvent;
        console.log(syntheticEvent);

    };

    const handleHttpError = (syntheticEvent) => {
        const { nativeEvent } = syntheticEvent;
    };

    function isHTMLString(str) {
        if (typeof str !== 'string') return false;
        return /<\/?[a-z][\s\S]*>/i.test(str.trim());
    }

    const webUrl = 'https://dev.starlinkinfluencers.in/web/public/starlink/vouchers?user_id=' + userId;
    const isHTML = isHTMLString(webUrl);
    const injectedJS = `
        (function() {
            // Force reload all images that failed to load
            var imgs = document.querySelectorAll('img');
            imgs.forEach(function(img) {
                if (!img.complete || img.naturalWidth === 0) {
                    var src = img.src;
                    img.src = '';
                    img.src = src + (src.indexOf('?') >= 0 ? '&' : '?') + '_t=' + Date.now();
                }
            });

            // Observe future images added dynamically
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(m) {
                    m.addedNodes.forEach(function(node) {
                        if (node.nodeName === 'IMG') {
                            node.addEventListener('error', function() {
                                var s = node.src;
                                node.src = '';
                                node.src = s + (s.indexOf('?') >= 0 ? '&' : '?') + '_t=' + Date.now();
                            });
                        }
                    });
                });
            });
            observer.observe(document.body, { childList: true, subtree: true });
        })();
        true;
    `;
    const origin = !isHTML ? getOrigin(webUrl) : undefined;
    const additionalHeaders = !isHTML
        ? {
            Referer: origin + '/',
            Origin: origin,
            'User-Agent':
                Platform.OS === 'android'
                    ? 'Mozilla/5.0 (Linux; Android 10; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36'
                    : 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
        }
        : undefined;

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
            <View style={{ width: '100%', height: '100%', position: 'absolute', flexDirection: 'column' }}>
                <View style={{ height: Platform.OS == 'ios' ? 25 : 0 }} />
                <View style={{ width: '100%', height: 70, backgroundColor: Colors.red }}>
                    <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center', height: '100%' }}>
                        <Text style={{ fontSize: 20, color: '#fff', fontWeight: '600' }}>{"Voucher"}</Text>
                    </View>
                    <View style={{ height: '100%', paddingHorizontal: 15, flexDirection: 'column', justifyContent: 'center', position: 'absolute' }}>
                        <TouchableOpacity onPress={() => {
                            setTimeout(() => {
                                props.navigation.goBack()
                            }, 500)
                        }}>
                            <Image style={{ height: 30, width: 30, }} source={Icons.back} />
                        </TouchableOpacity>
                    </View>
                </View>
                <WebView
                    ref={webViewRef}
                    source={
                        isHTML
                            ? { html: webUrl }
                            : { uri: webUrl, headers: additionalHeaders }
                    }
                    onNavigationStateChange={(navState) => setCanGoBack(navState.canGoBack)}
                    onLoadEnd={handleLoadEnd}
                    onError={handleError}
                    onHttpError={handleHttpError}

                    mixedContentMode="always"
                    domStorageEnabled={true}
                    javaScriptEnabled={true}
                    javaScriptCanOpenWindowsAutomatically={true}
                    allowUniversalAccessFromFileURLs={true}
                    allowFileAccessFromFileURLs={true}
                    thirdPartyCookiesEnabled={true}
                    sharedCookiesEnabled={true}

                    userAgent={
                        Platform.OS === 'android'
                            ? 'Mozilla/5.0 (Linux; Android 10; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36'
                            : 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1'
                    }
                    injectedJavaScript={injectedJS}
                    cacheEnabled={false}
                    cacheMode="LOAD_NO_CACHE"

                    style={{ flex: 1 }}
                />
            </View>

            {isLoading && (
                <Animated.View style={[styles.loaderOverlay, { opacity: fadeAnim }, { pointerEvents: 'none' }]} >
                    <View style={styles.loaderContent}>
                        <SpinnerRing color={Colors.main} />

                        <Text style={[styles.loaderTitle, { color: Colors.main }]}>
                            Loading Page
                        </Text>
                        <Text style={styles.loaderSubtitle}>
                            {'Please wait...'}
                        </Text>

                        <View style={styles.dotsRow}>
                            {[dot1, dot2, dot3].map((dot, i) => (
                                <Animated.View key={i} style={[styles.dot, { backgroundColor: Colors.main }, { transform: [{ translateY: dot }] },]} />
                            ))}
                        </View>
                    </View>
                </Animated.View>
            )}
        </SafeView>
    );
};

export default Vouchers;

const styles = StyleSheet.create({
    loaderOverlay: { ...StyleSheet.absoluteFillObject, backgroundColor: Colors.white, alignItems: 'center', justifyContent: 'center', zIndex: 10, },
    loaderContent: { alignItems: 'center', justifyContent: 'center', gap: 10, },
    spinnerRing: { width: 60, height: 60, borderRadius: 30, borderWidth: 5, borderColor: '#E5E7EB', marginBottom: 6, },
    loaderTitle: { fontSize: 18, fontWeight: '700', letterSpacing: 0.3, },
    loaderSubtitle: { fontSize: 13, color: '#9CA3AF', textAlign: 'center', lineHeight: 18, },
    dotsRow: { flexDirection: 'row', gap: 8, marginTop: 8, },
    dot: { width: 9, height: 9, borderRadius: 5, },
});