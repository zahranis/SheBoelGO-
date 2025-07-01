package com.example.finalproject.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.finalproject.R
import com.example.finalproject.model.BestSeller
import com.google.firebase.firestore.FirebaseFirestore
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

class BestSellerViewModel : ViewModel() {
    private val _bestSellers = MutableStateFlow<List<BestSeller>>(emptyList())
    val bestSellers: StateFlow<List<BestSeller>> = _bestSellers

    private val firestore = FirebaseFirestore.getInstance()

    private val imageMap = mapOf(
        "americano" to R.drawable.americano,
        "sushi" to R.drawable.sushi,
        "doughnut" to R.drawable.doughnut,
        "vanilla_ice_cream" to R.drawable.vanilla_ice_cream,
    )

    init {
        loadBestSellers()
    }

    private fun loadBestSellers() {
        viewModelScope.launch {
            firestore.collection("best_seller")
                .get()
                .addOnSuccessListener { result ->
                    val list = result.documents.mapNotNull { doc ->
                        val price = doc.getDouble("price") ?: return@mapNotNull null
                        val imageName = doc.getString("image_res") ?: ""
                        val imageRes = imageMap[imageName] ?: R.drawable.americano
                        BestSeller(
                            id = doc.id,
                            price = price,
                            imageRes = imageRes
                        )
                    }
                    _bestSellers.value = list
                }
                .addOnFailureListener {
                    _bestSellers.value = emptyList()
                }
        }
    }
}
