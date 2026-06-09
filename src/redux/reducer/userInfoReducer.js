// import {UPDATE_TEST, RESET_TEST} from '../constants/typeConstants'
import constants from '../../helper/constants/Constants'

const userInfoReducer = (state = [], action) => {
  switch (action.type) {
    case constants.UPDATE_USER: {
      return action.data
    }
    case constants.RESET_USER: {
      return ''
    }
    default:
      return state
  }
}
export const updateData = data => {
  return dispatch => {
    dispatch({
      type: constants.UPDATE_USER,
      data: data,
    })
  }
}
export const resetData = () => {
  return dispatch => {
    dispatch({
      type: constants.RESET_USER,
    })
  }
}
export default userInfoReducer
