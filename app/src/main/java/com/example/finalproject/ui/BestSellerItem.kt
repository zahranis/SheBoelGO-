package com.example.finalproject.ui

import android.annotation.SuppressLint
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.unit.dp
import androidx.compose.ui.Alignment
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.sp
import com.example.finalproject.R
import com.example.finalproject.model.BestSeller

@SuppressLint("DefaultLocale")
@Composable
fun BestSellerItem(
    bestSeller: BestSeller,
    modifier: Modifier = Modifier
) {
    val poppins = FontFamily(Font(R.font.poppins))
    val priceFormatted = String.format("$%.2f", bestSeller.price)

    Box(
        modifier = modifier
            .height(130.dp)
            .clip(RoundedCornerShape(16.dp))
    ) {
        Image(
            painter = painterResource(id = bestSeller.imageRes),
            contentDescription = priceFormatted,
            modifier = Modifier
                .fillMaxSize()
                .clip(RoundedCornerShape(16.dp)),
            contentScale = ContentScale.Crop
        )

        Box(
            modifier = Modifier
                .align(Alignment.BottomEnd)
                .padding(12.dp)
                .background(
                    color = Color(0xFF3B82F6),
                    shape = RoundedCornerShape(12.dp)
                )
                .padding(horizontal = 12.dp, vertical = 6.dp)
        ) {
            Text(
                text = priceFormatted,
                style = MaterialTheme.typography.titleSmall.copy(fontSize = 9.sp),
                color = Color.White,
                fontFamily = poppins,
                fontWeight = FontWeight.Bold
            )
        }
    }
}