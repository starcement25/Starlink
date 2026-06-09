import {combineReducers} from 'redux'
import userReducer from './userInfoReducer'

const allReducers = combineReducers({
  user: userReducer,
})
const rootReducer = (state, action) => {
  return allReducers(state, action)
}
export default rootReducer
