import React, { useCallback, useRef, useState } from 'react'
import {
  Text,
  View,
  TouchableOpacity,
  Modal,
  ActivityIndicator,
  FlatList,
  Image,
  Platform,
  TextInput,
  Linking,
  TouchableWithoutFeedback,
} from 'react-native'
import styles from './OrderStyle'
import Toolbar from '../../../components/toolbar/Toolbar'
import { postApiWithHeader, getApiWithHeader } from '../../../helper/http/Api'
import constants from '../../../helper/constants/Constants'
import Toast from 'react-native-toast-message'
import Support from '../support/Support'
import ViewSupport from '../viewSupport/ViewSupport'
import moment from 'moment'
import useTextValue from '../../../helper/constants/useTextValue'
import useMessageList from '../../../helper/constants/useMessageList'
import selectedLanguage from '../../../helper/constants/LanguageSelect'
import { convertForShowData } from '../../../helper/constants/NumberConverter'
import Icons from '../../../helper/image/ImageList'
import SafeView from '../../../helper/safeview/SafeView'
import { Colors } from '../../../helper/safeview/Colors'
import { useFocusEffect } from '@react-navigation/native'
import AsyncStorage from '@react-native-async-storage/async-storage'

const Order = (props) => {
  const textValue = useTextValue()
  const messageList = useMessageList()

  const [orderList, setOrderList] = useState([])
  const [modalViewSupport, setModalViewSupport] = useState(false)
  const [modalSupport, setModalSupport] = useState(false)
  const [modalVisibility, setModalVisibility] = useState(false)
  const [modalConfirmPopup, setModalConfirmPopup] = useState(false)
  const [modalNotDeliveredPopup, setModalNotDeliveredmPopup] = useState(false)
  const [modalFeedbackPopup, setModalFeedbackPopup] = useState(false)
  const [orderInfo, setOrderInfo] = useState(null)
  const [order_id, setOrder_id] = useState('')
  const [flatlistLoader, setFlatListLoader] = useState(true)
  const [page, setPage] = useState(1)
  const [hasMore, setHasMore] = useState(true)
  const [submittingFeedback, setSubmittingFeedback] = useState(false)

  const [selectedReason, setSelectedReason] = useState(null)
  const [otherText, setOtherText] = useState('')

  const isFocusRef = useRef(false)

  const radioOptions = ['Not Delivered', 'Defective Product', 'Other']

  useFocusEffect(
    useCallback(() => {
      isFocusRef.current = true
      setOrderList([])
      setPage(1)
      setHasMore(true)
      setFlatListLoader(true)
      my_profile()
      return () => {
        isFocusRef.current = false
      }
    }, [])
  )

  const showToast = (type, msg) => {
    Toast.show({
      type: type,
      text2: msg,
      text2NumberOfLines: 2,
    })
  }

  const my_profile = async () => {
    try {
      const res = await getApiWithHeader(
        constants.my_profile + '?preferred_app_lang=' + selectedLanguage()
      )
      if (!isFocusRef.current) return
      if (res?.data?.status) {
        setModalConfirmPopup(false)
        const userId = res?.data?.data?.id
        if (userId) {
          fetchOrders(userId, 1)
        } else {
          showToast('error', messageList.t4)
          setFlatListLoader(false)
        }
      } else {
        showToast('error', res?.data?.msg || messageList.t4)
        setFlatListLoader(false)
      }
    } catch (err) {
      showToast('error', messageList.t4)
      setFlatListLoader(false)
    }
  }

  /**
   * IMPORTANT CHANGE:
   * - Removed recursion that was calling up to 50 pages.
   * - Now it only loads a single page (pageValue), typically page 1 for refresh.
   *   This prevents the API from being called many times after actions like acknowledgment.
   */
  const fetchOrders = async (userId, pageValue = 1) => {
    if (!isFocusRef.current) return
    if (!hasMore && pageValue !== 1) return

    if (pageValue === 1) {
      setFlatListLoader(true)
    }

    try {
      const formData = new FormData()
      formData.append('user_id', userId)
      formData.append('preferred_app_lang', selectedLanguage())
      formData.append('page', String(pageValue))

      const response = await postApiWithHeader(constants.getOrder, formData)
      if (!isFocusRef.current) return

      if (response?.data?.status) {
        const items = Array.isArray(response.data.data) ? response.data.data : []
        //console.log('items------_>', items)

        setOrderList(items)
        setPage(pageValue)

        if (!items.length) {
          setHasMore(false)
        } else {
          setHasMore(true)
        }

        setFlatListLoader(false)
      } else {
        if (response?.data?.status_code == 401) {
          showToast('error', response?.data?.message || 'Unauthorized')
          await _logout()
        } else {
          if (pageValue === 1) {
            showToast('error', response?.data?.msg || messageList.t4)
          }
          setFlatListLoader(false)
          setHasMore(false)
        }
      }
    } catch (err) {
      setFlatListLoader(false)
      setHasMore(false)
      showToast('error', messageList.t4)
    }
  }

  const _logout = async () => {
    try {
      const keys = ['user_info', 'access_token']
      await AsyncStorage.multiRemove(keys)
      props.navigation.reset({
        index: 0,
        routes: [{ name: 'AuthStack' }],
      })
    } catch (e) {}
  }

  const close_modal = () => {
    setOrderInfo(null)
    setModalViewSupport(false)
    setModalSupport(false)
    setModalVisibility(false)
  }

  const requestForConfirmOrderDelivered = async (orderId, status) => {
    try {
      const endpoint = `${constants.confirmOrder.replace(/\/$/, '')}/${encodeURIComponent(
        orderId
      )}`

      const body = {
        acknowledgement_status: status,
      }

      const response = await postApiWithHeader(endpoint, body)

      if (response?.data?.status) {
        setOrderList([])
        setFlatListLoader(true)
        my_profile()
        showToast('success', response.data.msg || 'Confirmed')
        setModalConfirmPopup(false)
      } else {
        showToast('error', response?.data?.msg || messageList.t4)
      }
    } catch (err) {
      showToast('error', messageList.t4)
    }
  }

  const submitFeedBack = async (orderId, feedbackReason) => {
    try {
      setSubmittingFeedback(true)

      const token = await AsyncStorage.getItem('access_token')
      if (!token) {
        showToast('error', 'Not authenticated')
        setSubmittingFeedback(false)
        return
      }

      const url = `${constants.base_url}${constants.submit_order_feedback}/${orderId}`

      const requestOptions = {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          feedback: feedbackReason,
        }),
      }

      const response = await fetch(url, requestOptions)
      const result = await response.json()
      //console.log('abcd-----', result)
      if (result?.status) {
        showToast('success', result?.msg || 'Feedback submitted')
        setModalFeedbackPopup(false)
        setOtherText('')
        my_profile()
      } else {
        showToast('error', result?.msg || 'Failed to submit feedback')
        setModalFeedbackPopup(false)
      }
    } catch (error) {
      console.error('Feedback API error:', error)
      showToast('error', messageList.t4)
    } finally {
      setSubmittingFeedback(false)
    }
  }

  const handleSubmit = () => {
    const feedbackReason = otherText?.trim()
    if (!feedbackReason) {
      showToast('error', 'Please enter your feedback.')
      return
    }
    submitFeedBack(order_id, feedbackReason)
  }

  const renderFooter = useCallback(() => {
    if (!flatlistLoader) return null
    return (
      <View style={{ paddingHorizontal: 20, paddingTop: 20, alignItems: 'center' }}>
        <ActivityIndicator size="large" color={Colors.red || '#ee1d23'} />
      </View>
    )
  }, [flatlistLoader])

  const keyExtractor = useCallback((item, index) => {
    if (!item) return String(index)
    if (item.id !== undefined && item.id !== null) return String(item.id)
    if (item.order_id !== undefined && item.order_id !== null) return String(item.order_id)
    return String(index)
  }, [])

  // Helper to build row-wise text for Points block
  const buildPointsBlock = (item) => {
    const catalogue = item?.catalogue_point
    const tds = item?.tds_point
    const total = item?.point

    // If all 3 are present, show detailed breakdown:
    // Points - catalogue_point
    // TDS    - tds_point
    // Total  - point
    if (
      catalogue !== null &&
      catalogue !== undefined &&
      tds !== null &&
      tds !== undefined &&
      total !== null &&
      total !== undefined
    ) {
      return (
        `Points - ${convertForShowData(catalogue)}\n` +
        `TDS - ${convertForShowData(tds)}\n` +
        `Total - ${convertForShowData(total)}`
      )
    }

    // Fallback: show the original point only
    return convertForShowData(item?.point)
  }

  const renderItem = useCallback(
    ({ item }) => {
      if (!item) return null

      return (
        <View
          style={{
            width: '100%',
            borderWidth: 1,
            borderColor: '#FFDFE1',
            paddingHorizontal: 5,
            paddingVertical: 5,
            borderRadius: 5,
            marginBottom: 10,
            backgroundColor: 'white',
          }}
        >
          <View
            style={{
              flexDirection: 'row',
              minHeight: 40,
              padding: 5,
              justifyContent: 'center',
              alignItems: 'center',
            }}
          >
            <View style={{ flex: 1, justifyContent: 'center' }}>
              <Text style={{ color: 'gray', fontSize: 15 }}>
                {convertForShowData(textValue.Order_Id)} :
              </Text>
            </View>
            <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
            <View style={{ flex: 1, justifyContent: 'center', paddingLeft: 10 }}>
              <Text style={{ color: 'black', fontSize: 15 }}>
                {convertForShowData(item.order_id)}
              </Text>
            </View>
          </View>

          <View
            style={{
              flexDirection: 'row',
              minHeight: 40,
              padding: 5,
              backgroundColor: '#FFF0F1',
              borderRadius: 5,
              justifyContent: 'center',
              alignItems: 'center',
              marginTop: 6,
            }}
          >
            <View style={{ flex: 1, justifyContent: 'center' }}>
              <Text style={{ color: 'gray', fontSize: 15 }}>
                {convertForShowData(textValue.Date)} :
              </Text>
            </View>
            <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
            <View style={{ flex: 1, justifyContent: 'center', paddingLeft: 10 }}>
              <Text style={{ color: 'black', fontSize: 15 }}>
                {convertForShowData(item.reward_date)}
              </Text>
            </View>
          </View>

          <View
            style={{
              flexDirection: 'row',
              minHeight: 40,
              padding: 5,
              justifyContent: 'center',
              alignItems: 'center',
              marginTop: 6,
            }}
          >
            <View style={{ flex: 1, justifyContent: 'center' }}>
              <Text style={{ color: 'gray', fontSize: 15 }}>
                {convertForShowData(textValue.Description)} :
              </Text>
            </View>
            <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
            <View style={{ flex: 1, justifyContent: 'center', paddingLeft: 10 }}>
              <Text style={{ color: 'black', fontSize: 15 }}>
                {convertForShowData(item.description)}
              </Text>
            </View>
          </View>

          {/* 🔹 UPDATED POINTS ROW WITH ROW-WISE BREAKDOWN */}
          <View
            style={{
              flexDirection: 'row',
              minHeight: 40,
              padding: 5,
              backgroundColor: '#FFF0F1',
              borderRadius: 5,
              justifyContent: 'center',
              alignItems: 'center',
              marginTop: 6,
            }}
          >
            <View style={{ flex: 1, justifyContent: 'center' }}>
              <Text style={{ color: 'gray', fontSize: 15 }}>
                {convertForShowData(textValue.Points)} :
              </Text>
            </View>
            <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
            <View style={{ flex: 1, justifyContent: 'center', paddingLeft: 10 }}>
              <Text style={{ color: 'black', fontSize: 15 }}>
                {buildPointsBlock(item)}
              </Text>
            </View>
          </View>

          <View
            style={{
              flexDirection: 'row',
              minHeight: 40,
              padding: 5,
              justifyContent: 'center',
              alignItems: 'center',
              marginTop: 6,
            }}
          >
            <View style={{ flex: 1, justifyContent: 'center' }}>
              <Text style={{ color: 'gray', fontSize: 15 }}>
                {convertForShowData(textValue.Delivery_Status)} :
              </Text>
            </View>
            <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
            <View style={{ flex: 1, justifyContent: 'center', paddingLeft: 10 }}>
              <Text style={{ color: 'black', fontSize: 15 }}>{item.delivery_status_value}</Text>
            </View>
          </View>
          {item?.remarks && 
          <View
            style={{
              flexDirection: 'row',
              minHeight: 40,
              padding: 5,
              backgroundColor: '#FFF0F1',
              justifyContent: 'center',
              alignItems: 'center',
              marginTop: 6,
            }}
          >
            <View style={{ flex: 1, justifyContent: 'center' }}>
              <Text style={{ color: 'gray', fontSize: 15 }}>
                {convertForShowData(textValue.Remarks)} :
              </Text>
            </View>
            <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
            <View style={{ flex: 1, justifyContent: 'center', paddingLeft: 10 }}>
              <Text style={{ color: 'black', fontSize: 15 }}>{item.remarks}</Text>
            </View>
          </View>}

          {item.delivery_date ? (
            <View
              style={{
                flexDirection: 'row',
                minHeight: 40,
                padding: 5,
                backgroundColor: '#FFF0F1',
                borderRadius: 5,
                justifyContent: 'center',
                alignItems: 'center',
                marginTop: 6,
              }}
            >
              <View style={{ flex: 1, justifyContent: 'center' }}>
                <Text style={{ color: 'gray', fontSize: 15 }}>
                  {convertForShowData(textValue.Delivery_Date)} :
                </Text>
              </View>
              <View style={{ width: 1, height: '65%', backgroundColor: '#FFD5D6' }} />
              <View style={{ flex: 1, justifyContent: 'center', paddingLeft: 10 }}>
                <Text style={{ color: 'black', fontSize: 15 }}>
                  {convertForShowData(moment(item?.delivery_date).format('DD-MM-YYYY'))}
                </Text>
              </View>
            </View>
          ) : null}

          <View
            style={{ flexDirection: 'column', justifyContent: 'space-between', marginTop: 10 }}
          >
            {item?.isConfirmEnabled ? (
              <TouchableOpacity
                onPress={() => {
                  setOrder_id(item.order_id)
                  setModalConfirmPopup(true)
                }}
                style={{
                  flex: 1,
                  backgroundColor: '#ee1d23',
                  paddingVertical: 10,
                  borderRadius: 5,
                  elevation: 5,
                  alignItems: 'center',
                  justifyContent: 'center',
                  marginBottom: 8,
                }}
              >
                <Text style={{ color: 'white', fontSize: 14, fontWeight: '600' }}>
                  Acknowledge Delivery
                </Text>
              </TouchableOpacity>
            ) : null}
            {item?.isConfirmEnabled ? (
              <TouchableOpacity
                onPress={() => {
                  setOrder_id(item.order_id)
                  setModalNotDeliveredmPopup(true)
                }}
                style={{
                  flex: 1,
                  backgroundColor: '#fce3e5',
                  paddingVertical: 10,
                  borderRadius: 5,
                  elevation: 5,
                  alignItems: 'center',
                  justifyContent: 'center',
                  marginBottom: 8,
                }}
              >
                <Text style={{ color: '#ee1d23', fontSize: 14, fontWeight: '600' }}>
                  Not Delivered
                </Text>
              </TouchableOpacity>
            ) : null}

            {item?.order_tracking_url ? (
              <TouchableOpacity
                onPress={async () => {
                  const url = item?.order_tracking_url.trim()
                  if (!url) {
                    showToast('error', 'No tracking URL provided')
                    return
                  }
                  try {
                      await Linking.openURL(url)
                  } catch (e) {
                    showToast('error', "Can't open this URL: ")
                  }
                }}
                style={{
                  flex: 1,
                  backgroundColor: '#ee1d23',
                  paddingVertical: 10,
                  borderRadius: 5,
                  alignItems: 'center',
                  justifyContent: 'center',
                  marginBottom: 8,
                }}
              >
                <Text style={{ color: 'white', fontSize: 14, fontWeight: '600' }}>
                  Track Order
                </Text>
              </TouchableOpacity>
            ) : null}

            {item?.is_feedback_button_active ? (
              <TouchableOpacity
                onPress={() => {
                  setModalFeedbackPopup(true)
                  setOrder_id(item.order_id)
                }}
                style={{
                  flex: 1,
                  backgroundColor: '#ffd4d7',
                  paddingVertical: 10,
                  borderRadius: 5,
                  alignItems: 'center',
                  justifyContent: 'center',
                }}
              >
                <Text style={{ color: '#ee1d23', fontSize: 14, fontWeight: '600' }}>
                  Feedback / Complaint
                </Text>
              </TouchableOpacity>
            ) : null}
          </View>
        </View>
      )
    },
    [textValue, selectedReason, otherText]
  )

  // BACK button handler: deterministic and safe
  const handleBackPress = () => {
    try {
      if (props?.navigation?.getState) {
        //console.log('Navigation state on back press:', props.navigation.getState())
      }

      if (props?.navigation?.canGoBack && props.navigation.canGoBack()) {
        props.navigation.goBack()
        return
      }

      if (props?.navigation?.popToTop) {
        props.navigation.popToTop()
        return
      }

      props.navigation.reset({
        index: 0,
        routes: [{ name: 'Home' }],
      })
    } catch (e) {
      console.warn('Back navigation failed:', e)
    }
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.red}>
      <View
        style={{
          width: '100%',
          height: '100%',
          flexDirection: 'column',
          backgroundColor: '#FFF',
        }}
      >
        <View
          style={{
            width: '100%',
            height: 100,
            borderBottomLeftRadius: 25,
            borderBottomRightRadius: 25,
            backgroundColor: '#EE1D23',
          }}
        />
      </View>

      <View
        style={{
          width: '100%',
          height: '100%',
          position: 'absolute',
          flexDirection: 'column',
        }}
      >
        <View style={{ height: Platform.OS == 'ios' ? 25 : 0 }} />
        <View style={{ width: '100%', height: 70 }}>
          <View
            style={{
              width: '100%',
              alignItems: 'center',
              justifyContent: 'center',
              height: '100%',
            }}
          >
            <Text style={styles._upperView._txt}>
              {convertForShowData(textValue.ORDER_LIST)}
            </Text>
          </View>
          <View
            style={{
              height: '100%',
              paddingHorizontal: 15,
              flexDirection: 'column',
              justifyContent: 'center',
              position: 'absolute',
            }}
          >
            <TouchableOpacity onPress={handleBackPress}>
              <Image style={styles._upperView._back._img} source={Icons.back} />
            </TouchableOpacity>
          </View>
        </View>

        <View style={{ width: '100%', flex: 1, paddingHorizontal: 30 }}>
          <View
            style={{
              width: '100%',
              height: '100%',
              backgroundColor: '#FFF',
              borderTopLeftRadius: 20,
              borderTopRightRadius: 20,
              paddingVertical: 15,
              paddingHorizontal: 5,
            }}
          >
            <FlatList
              showsHorizontalScrollIndicator={false}
              showsVerticalScrollIndicator={false}
              ItemSeparatorComponent={() => <View style={{ height: 10 }} />}
              data={orderList}
              renderItem={renderItem}
              ListFooterComponent={renderFooter}
              keyExtractor={keyExtractor}
              contentContainerStyle={{ paddingBottom: 40 }}
              ListEmptyComponent={() =>
                !flatlistLoader ? (
                  <View style={{ alignItems: 'center', marginTop: 30 }}>
                    <Text style={{ color: '#666' }}>No orders found</Text>
                  </View>
                ) : null
              }
            />
          </View>
        </View>
      </View>

      <View style={styles._bgColor}>
        <View style={styles._upperView}>
          <Text style={styles._upperView._txt}>
            {convertForShowData(textValue.ORDER_LIST)}
          </Text>
          <Text
            style={{
              color: '#fff',
              position: 'absolute',
              fontSize: 20,
              fontWeight: '500',
            }}
          >
            {convertForShowData(textValue.ORDER_LIST)}
          </Text>
          <Toolbar obj={{ title: '', icon: '', language: 'notshow' }} />
        </View>

        <View style={styles._lowerView} />
      </View>

      <Modal
        animationType="fade"
        transparent={true}
        visible={modalVisibility}
        onRequestClose={() => setModalVisibility(false)}
      >
        {modalSupport ? <Support obj={{ item: orderInfo }} sendData={close_modal} /> : null}
        {modalViewSupport ? (
          <ViewSupport obj={{ item: orderInfo }} sendData={close_modal} />
        ) : null}
      </Modal>

      <Modal
        animationType="fade"
        transparent={true}
        visible={modalNotDeliveredPopup}
        onRequestClose={() => setModalNotDeliveredmPopup(false)}
      >
        <TouchableWithoutFeedback onPress={() => setModalNotDeliveredmPopup(false)}>
          <View
            style={{
              flex: 1,
              backgroundColor: '#0009',
              alignItems: 'center',
              justifyContent: 'center',
            }}
          >
            <TouchableWithoutFeedback>
              <View
                style={{
                  width: '80%',
                  padding: 20,
                  backgroundColor: '#FFF',
                  borderRadius: 10,
                }}
              >
                <Text
                  style={{
                    color: '#000',
                    fontSize: 16,
                    marginBottom: 10,
                    fontWeight: '600',
                  }}
                >
                  Confirm
                </Text>

                <Text style={{ color: '#555', fontSize: 14 }}>
                  Are you sure that <Text style={{ fontWeight: 'bold' }}>{order_id}</Text> is
                  <Text style={{ fontWeight: 'bold', color: 'red' }}> not delivered </Text> to you?
                </Text>

                <View style={{ height: 10 }} />

                <View style={{ width: '100%', flexDirection: 'row-reverse' }}>
                  <View style={{ width: 30 }} />
                  <TouchableOpacity
                    onPress={() => {
                      requestForConfirmOrderDelivered(order_id, 0)
                      setModalNotDeliveredmPopup(false)
                    }}
                  >
                    <Text style={{ color: '#060', fontSize: 14 }}>Yes</Text>
                  </TouchableOpacity>
                   <View style={{ width: 30 }} />
                  <TouchableOpacity
                    onPress={() => {
                      //requestForConfirmOrderDelivered(order_id, 0)
                      setModalNotDeliveredmPopup(false)
                    }}
                  >
                    <Text style={{ color: 'red', fontSize: 14 }}>Cancel</Text>
                  </TouchableOpacity>
                </View>
              </View>
            </TouchableWithoutFeedback>
          </View>
        </TouchableWithoutFeedback>
      </Modal>

      <Modal
        animationType="fade"
        transparent={true}
        visible={modalConfirmPopup}
        onRequestClose={() => setModalConfirmPopup(false)}
      >
        <TouchableWithoutFeedback onPress={() => setModalConfirmPopup(false)}>
          <View
            style={{
              flex: 1,
              backgroundColor: '#0009',
              alignItems: 'center',
              justifyContent: 'center',
            }}
          >
            <TouchableWithoutFeedback>
              <View
                style={{
                  width: '80%',
                  padding: 20,
                  backgroundColor: '#FFF',
                  borderRadius: 10,
                }}
              >
                <Text
                  style={{
                    color: '#000',
                    fontSize: 16,
                    marginBottom: 10,
                    fontWeight: '600',
                  }}
                >
                  Confirm
                </Text>

                <Text style={{ color: '#555', fontSize: 14 }}>
                  Are you sure that <Text style={{ fontWeight: 'bold' }}>{order_id}</Text> is
                  <Text style={{ fontWeight: 'bold', color: '#060' }}> delivered </Text> to you?
                </Text>

                <View style={{ height: 10 }} />

                <View style={{ width: '100%', flexDirection: 'row-reverse' }}>
                  <View style={{ width: 30 }} />
                  <TouchableOpacity
                    onPress={() => {
                      requestForConfirmOrderDelivered(order_id, 1)
                      setModalConfirmPopup(false)
                    }}
                  >
                    <Text style={{ color: '#060', fontSize: 14 }}>Yes</Text>
                  </TouchableOpacity>
                   <View style={{ width: 30 }} />
                  <TouchableOpacity
                    onPress={() => {
                      //requestForConfirmOrderDelivered(order_id, 1)
                      setModalConfirmPopup(false)
                    }}
                  >
                    <Text style={{ color: 'red', fontSize: 14 }}>Cancel</Text>
                  </TouchableOpacity>
                </View>
              </View>
            </TouchableWithoutFeedback>
          </View>
        </TouchableWithoutFeedback>
      </Modal>

      <Modal
        animationType="fade"
        transparent={true}
        visible={modalFeedbackPopup}
        onRequestClose={() => setModalFeedbackPopup(false)}
      >
        <View
          style={{
            width: '100%',
            height: '100%',
            backgroundColor: '#0009',
            alignItems: 'center',
            justifyContent: 'center',
          }}
        >
          <View
            style={{
              width: '85%',
              padding: 20,
              backgroundColor: '#FFF',
              borderRadius: 10,
            }}
          >
            <Text
              style={{
                color: '#000',
                fontSize: 18,
                fontWeight: '600',
                marginBottom: 10,
              }}
            >
              Feedback / Complaint
            </Text>

            <Text
              style={{
                color: '#555',
                fontSize: 14,
                marginBottom: 10,
              }}
            >
              Please provide your feedback for order{' '}
              <Text style={{ fontWeight: 'bold' }}>{order_id}</Text>:
            </Text>

            <TextInput
              value={otherText}
              onChangeText={setOtherText}
              placeholder="Write your feedback here..."
              placeholderTextColor="#999"
              multiline={true}
              numberOfLines={4}
              style={{
                borderWidth: 1,
                borderColor: '#CCC',
                borderRadius: 8,
                padding: 12,
                minHeight: 100,
                textAlignVertical: 'top',
                color: '#000',
              }}
            />

            <View
              style={{
                flexDirection: 'row',
                justifyContent: 'flex-end',
                marginTop: 18,
              }}
            >
              <TouchableOpacity
                onPress={() => {
                  setModalFeedbackPopup(false)
                  setOtherText('')
                }}
                style={{ marginRight: 16, justifyContent: 'center' }}
              >
                <Text style={{ color: Colors.red, fontSize: 15 }}>Cancel</Text>
              </TouchableOpacity>

              <TouchableOpacity
                onPress={handleSubmit}
                disabled={!otherText.trim() || submittingFeedback}
                style={{
                  backgroundColor:
                    !otherText.trim() || submittingFeedback ? '#f2a6a8' : Colors.red,
                  paddingVertical: 10,
                  paddingHorizontal: 18,
                  borderRadius: 6,
                  justifyContent: 'center',
                  alignItems: 'center',
                }}
              >
                {submittingFeedback ? (
                  <ActivityIndicator size="small" color="#fff" />
                ) : (
                  <Text style={{ color: '#FFF', fontSize: 15 }}>Submit</Text>
                )}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </SafeView>
  )
}

export default Order
